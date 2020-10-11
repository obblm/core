<?php

namespace Obblm\Core\Form\Team;

use Doctrine\Common\Collections\Criteria;
use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\Team;
use Obblm\Core\Form\Player\PlayerTeamType;
use Obblm\Core\Listener\ChangePlayerPositionSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RuleHelperInterface $helper */
        $helper = $options['helper'];
        $builder->add('name')
            ->add('anthem')
            ->add('fluff')
            ->add('ready');

        if ($builder->getData()) {
            $team = $builder->getData();

            $positions = $helper->getAvailablePlayerForTeamCreation($team);
            $builder->add('players', CollectionType::class, [
                'entry_type' => PlayerTeamType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => true,
                'entry_options' => [
                    'helper' => $helper,
                    'positions' => $positions,
                    'roster' => $helper->getRoster($team)
                ]
            ]);
            $builder->addEventSubscriber(new ChangePlayerPositionSubscriber());
            $builder->setDataMapper($this);
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
        $forms['players']->setData($newPlayerList->matching($criteria));

        $forms['name']->setData($viewData->getName());
        $forms['anthem']->setData($viewData->getAnthem());
        $forms['fluff']->setData($viewData->getFluff());
        $forms['ready']->setData($viewData->getReady());
    }

    public function mapFormsToData(iterable $forms, &$viewData)
    {
        /** @var Team $viewData */
        if ($viewData === null) {
            return;
        }
        $forms = iterator_to_array($forms);
        /** @var Player[] $players */
        $players = $forms['players']->getData();
        $viewData->setName($forms['name']->getData())
            ->setAnthem($forms['anthem']->getData())
            ->setFluff($forms['fluff']->getData())
            ->setReady($forms['ready']->getData());
        foreach ($players as $player) {
            if (!$player->getPosition()) {
                $viewData->removePlayer($player);
            }

            if (!$player->getName()) {
                $player->setName('P ' . $player->getNumber());
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'obblm',
            'helper' => null,
            'data_class' => Team::class,
        ));

        $resolver->setAllowedTypes('helper', [RuleHelperInterface::class]);
    }
}
