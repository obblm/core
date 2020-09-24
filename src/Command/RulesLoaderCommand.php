<?php

namespace Obblm\Core\Command;

use Obblm\Core\Entity\Rule;
use Doctrine\ORM\EntityManagerInterface;
use Obblm\Core\Helper\CoreTranslation;
use Obblm\Core\Helper\Rule\Config\ConfigResolver;
use Obblm\Core\Helper\Rule\Config\RuleConfigResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;

class RulesLoaderCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'obblm:rules:load';
    /** @var string */
    protected $rulesDirectory = 'datas/rules';
    /** @var EntityManagerInterface */
    private $em;
    /** @var SymfonyStyle */
    private $io;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->rulesDirectory = dirname(__DIR__).'/Resources/datas/rules';
        parent::__construct();
    }

    protected function configure():void
    {
        $this->setDescription('Loads Blood Bowl core rules.')
            ->setHelp('This command will add core Blood Bowl rules to the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $finder = new Finder();
        $this->io->title("Importing pre fetched rules from " . $this->rulesDirectory);
        $finder->directories()->ignoreDotFiles(true)->depth(0)->in($this->rulesDirectory);
        if ($finder->hasResults()) {
            foreach ($finder as $ruleDirectory) {
                $this->parseRule($ruleDirectory);
            }
            $this->em->flush();
            $this->io->success('All rules have been created or updated.');
            return 0;
        }

        return 1;
    }

    protected function parseRule(SplFileInfo $ruleDirectory):void
    {
        $rules = [];
        $key = $ruleDirectory->getFilename();
        $this->io->block("Importing {$key} rules form {$ruleDirectory->getPathname()}");
        $finder = new Finder();
        $finder->files()->ignoreDotFiles(true)->name(['*.yaml', '*.yml'])->in($ruleDirectory->getPathname());
        if ($finder->hasResults()) {
            $this->io->progressStart($finder->count());
            foreach ($finder as $file) {
                $content = Yaml::parseFile($file->getPathname());
                $rules = array_merge_recursive($rules, $content);
                $this->io->progressAdvance(1);
            }
            if (!isset($rules['rules'][$key])) {
                $this->io->error("The rules.{$key} rule does not exist in {$ruleDirectory->getPathname()} directory.");
            }
            $ruleArray = $rules['rules'][$key];

            try {
                $treeResolver = new ConfigResolver(new RuleConfigResolver());
                $ruleArray = $treeResolver->resolve($ruleArray);

                /** @var Rule|null $rule */
                $rule = $this->em->getRepository(Rule::class)->findOneBy(['ruleKey' => $key]);
                if (!$rule) {
                    $rule = (new Rule())
                        ->setRuleKey($key)
                        ->setReadOnly(true);
                }
                ksort($ruleArray['rosters']);
                $rule->setName(CoreTranslation::getRuleTitle($key))
                    ->setPostBb2020($ruleArray['post_bb_2020'] ?? false)
                    ->setTemplate($ruleArray['template'] ?? 'base')
                    ->setRule($ruleArray);
                $this->em->persist($rule);
                $this->io->progressFinish();
            } catch (InvalidArgumentException $e) {
                $this->io->progressFinish();
                $this->io->error("The rule.{$key} is not valid, resolver said :\n{$e->getMessage()}");
            }
            return;
        }
        $this->io->warning("There is no rule files in {$ruleDirectory->getPathname()} directory.");
        return;
    }
}
