<?php

namespace Obblm\Core\Application\ParamConverter;

use Obblm\Core\Domain\Model\Team;
use Obblm\Core\Domain\Repository\TeamRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class TeamConverter implements ParamConverterInterface
{
    /** @var TeamRepositoryInterface */
    protected $teamRepository;

    public function __construct(TeamRepositoryInterface $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $id = $request->get('article');
        $class = $request->get('class');
        if (!$id) {
            return false;
        }
        switch ($class) {
            case Team::class:
                $request->attributes->set($configuration->getName(), $this->teamRepository->get($id));
                break;
            default:
                throw new \Exception(sprintf('Expected an instance of %s. Got: %s', Team::class, $class));
        }

        return true;
    }

    public function supports(ParamConverter $configuration)
    {
        return 'team' === $configuration->getName();
    }
}
