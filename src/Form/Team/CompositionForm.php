<?php

namespace Obblm\Core\Form\Team;

use Doctrine\Common\Collections\Criteria;
use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Entity\Team;
use Obblm\Core\Exception\NoVersionException;
use Obblm\Core\Form\Player\PlayerTeamType;
use Obblm\Core\Helper\PlayerHelper;
use Obblm\Core\Helper\TeamHelper;
use Obblm\Core\Listener\UploaderSubscriber;
use Obblm\Core\Service\FileTeamUploader;
use Obblm\Core\Validator\Constraints\Team\Composition;
use Obblm\Core\Validator\Constraints\Team\Value;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CompositionForm extends AbstractType implements DataMapperInterface
{
    protected $uploader;

    public function __construct(FileTeamUploader $uploader)
    {
        $this->uploader = $uploader;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RuleHelperInterface $helper */
        $helper = $options['helper'];
        $builder->add('name')
            ->add('anthem')
            ->add('fluff');

        if ($builder->getData()) {
            /** @var Team $team */
            $team = $builder->getData();
            $roster = $helper->getRoster($team);

            $builder->add('players', CollectionType::class, [
                'entry_type' => PlayerTeamType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => true,
                'mapped' => false,
                'entry_options' => [
                    'helper' => $helper,
                    'positions' => $helper->getAvailablePlayerForTeamCreation($team),
                    'roster' => $helper->getRoster($team)
                ]
            ]);
            $builder->add('logo', FileType::class, [
                    'label' => 'logo',
                    'help' => 'obblm.help.image',
                    'help_translation_parameters' => ['formats' => 'jpg, jpeg, png, svg'],
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/jpg',
                                'image/jpeg',
                                'image/png',
                                'image/svg',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid image file',
                        ])
                    ],
                ])
                ->add('cover', FileType::class, [
                    'label' => 'cover',
                    'help' => 'obblm.help.image',
                    'help_translation_parameters' => ['formats' => 'jpg, jpeg, png, svg'],
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'image/svg',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid image file',
                        ])
                    ],
                ])
                ->add('firstVersion', TeamVersionType::class);

            $builder->setDataMapper($this);
            $builder->addEventSubscriber(new UploaderSubscriber($this->uploader));
        }
    }

    public function mapDataToForms($viewData, iterable $forms)
    {
        if ($viewData === null) {
            return;
        }
        if (!$viewData instanceof Team) {
            throw new UnexpectedTypeException($viewData, Team::class);
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        // In want to have 16 players in the list, no less, no more
        $usedNumbers = [];
        $newPlayerList = $viewData->getPlayers();
        foreach ($newPlayerList as $player) {
            $usedNumbers[$player->getNumber()] = $player;
        }
        for ($i=1; $i<=16; $i++) {
            if (!isset($usedNumbers[$i])) {
                $newPlayerList->add((new Player())->setNumber($i));
            }
        }
        $criteria = Criteria::create();
        $criteria->orderBy(['number' => 'ASC']);
        $forms['name']->setData($viewData->getName());
        $forms['fluff']->setData($viewData->getFluff());
        $forms['anthem']->setData($viewData->getAnthem());
        $forms['players']->setData($newPlayerList->matching($criteria));
        $version = TeamHelper::getLastVersion($viewData);
        $forms['firstVersion']->setData($version);
        if (isset($forms['apothecary'])) {
            $forms['apothecary']->setData($version->getApothecary());
        }
    }

    public function mapFormsToData(iterable $forms, &$viewData)
    {
        if ($viewData === null) {
            return;
        }
        if (!$viewData instanceof Team) {
            throw new UnexpectedTypeException($viewData, Team::class);
        }

        /** @var FormInterface[] $formChilds */
        $formChilds = iterator_to_array($forms);

        /** @var RuleHelperInterface $helper */
        $helper = $formChilds['name']->getParent()->getConfig()->getOption('helper');

        $viewData->setName($formChilds['name']->getData());
        $viewData->setAnthem($formChilds['anthem']->getData());
        $viewData->setFluff($formChilds['fluff']->getData());
        $version = TeamHelper::getLastVersion($viewData);
        /** @var FormInterface[] $formVersion */
        $formVersion = $formChilds['firstVersion'];
        $version->setRerolls($formVersion['rerolls']->getData());
        $version->setAssistants($formVersion['assistants']->getData());
        $version->setCheerleaders($formVersion['cheerleaders']->getData());
        $version->setPopularity($formVersion['popularity']->getData());

        foreach ($viewData->getPlayers() as $player) {
            $this->parsePlayer($viewData, $player, $helper);
        }
    }

    private function parsePlayer(&$viewData, Player $player, RuleHelperInterface $helper):void
    {
        if ($player instanceof Player && !$player->getPosition()) {
            $viewData->removePlayer($player);
            return;
        }
        $player->setTeam($viewData);
        $position = $helper->getPlayerPosition($player);
        try {
            $helper->setPlayerDefaultValues(PlayerHelper::getLastVersion($player), $position);
        } catch (NoVersionException $e) { // It's a new player !
            $teamVersion = TeamHelper::getLastVersion($player->getTeam());
            $playerVersion = new PlayerVersion();
            $player->addVersion($playerVersion);
            $teamVersion->addPlayerVersion($playerVersion);
            $helper->setPlayerDefaultValues($playerVersion, $position);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'obblm',
            'data_class' => Team::class,
            'helper' => null,
            'constraints' => [
                new Composition(),
                new Value(),
            ]
        ]);

        $resolver->setAllowedTypes('helper', [RuleHelperInterface::class]);
    }
}
