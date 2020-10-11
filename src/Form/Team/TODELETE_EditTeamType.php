<?php

namespace Obblm\Core\Form\Team;

use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Listener\UploaderSubscriber;
use Obblm\Core\Service\FileTeamUploader;
use Obblm\Core\Validator\Constraints\Team\Composition;
use Obblm\Core\Validator\Constraints\Team\Value;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class TODELETE_EditTeamType extends AbstractType
{
    protected $ruleHelper;
    protected $uploader;

    public function __construct(RuleHelper $ruleHelper, FileTeamUploader $uploader)
    {
        $this->ruleHelper = $ruleHelper;
        $this->uploader = $uploader;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($builder->getData() && $builder->getData() instanceof TeamVersion) {
            $team = $builder->getData()->getTeam();
            $helper = $this->ruleHelper->getHelper($team);
            if (!$team->isLockedByManagment() && !$team->isReady()) {
                $builder
                    ->add('team', TeamType::class, [
                        'data' => $team,
                        'helper' => $helper
                    ])
                    ->add('logo', FileType::class, [
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
                    ->add('rerolls', null, [
                        'attr' => ['min' => 0, 'max' => 8]
                    ])
                    ->add('cheerleaders', null, [
                        'attr' => ['min' => 0]
                    ])
                    ->add('assistants', null, [
                        'attr' => ['min' => 0]
                    ])
                    ->add('popularity', null, [
                        'attr' => ['min' => 0, 'max' => 9]
                    ]);
                $builder->addEventSubscriber(new UploaderSubscriber($this->uploader));
                if ($helper->couldHaveApothecary($team)) {
                    $builder->add('apothecary');
                }
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TeamVersion::class,
            'translation_domain' => 'obblm',
            'constraints' => [
                new Value(),
                new Composition(),
            ],
        ));
    }
}
