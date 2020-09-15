<?php

namespace Obblm\Core\Form\Team;

use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Helper\TeamHelper;
use Obblm\Core\Validator\Constraints\TeamComposition;
use Obblm\Core\Validator\Constraints\TeamValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditTeamType extends AbstractType
{
    protected $teamHelper;

    public function __construct(TeamHelper $teamHelper)
    {
        $this->teamHelper = $teamHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($version = $builder->getData()) {
            $team = $version->getTeam();
            //TODO: add listener to lock the team
            $locked = false;
            if (!$locked) {
                $builder
                    ->add('team', TeamType::class, [
                        'data' => $team
                    ])
                    ->add('rerolls')
                    ->add('cheerleaders')
                    ->add('assistants')
                    ->add('popularity');
                if ($this->teamHelper->couldHaveApothecary($team)) {
                    $builder->add('apothecary');
                }
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TeamVersion::class,
            'constraints' => [
                new TeamValue(),
                new TeamComposition(),
            ],
        ));
    }
}
