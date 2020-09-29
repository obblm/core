<?php

namespace Obblm\Core\Entity;

use Symfony\Component\Serializer\Annotation\Ignore;
use Obblm\Core\Repository\RuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RuleRepository::class)
 * @ORM\Table(name="obblm_rule")
 */
class Rule
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true, length=255)
     */
    private $ruleKey;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ruleDirectory;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $template;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $rule = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $postBb2020;

    /**
     * @ORM\Column(type="boolean")
     */
    private $readOnly;

    /**
     * @ORM\OneToMany(targetEntity=Team::class, mappedBy="rule")
     * @Ignore()
     */
    private $teams;

    protected $injuryTable = [];

    public function __construct()
    {
        $this->postBb2020 = false;
        $this->readOnly = false;
        $this->teams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRuleKey(): ?string
    {
        return $this->ruleKey;
    }

    public function setRuleKey(string $ruleKey): self
    {
        $this->ruleKey = $ruleKey;

        return $this;
    }

    public function getRuleDirectory(): ?string
    {
        return $this->ruleDirectory;
    }

    public function setRuleDirectory(string $ruleDirectory): self
    {
        $this->ruleDirectory = $ruleDirectory;

        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRule(): ?array
    {
        return $this->rule;
    }

    public function setRule(?array $rule): self
    {
        $this->rule = $rule;
        $this->constructInjuryTable($rule);

        return $this;
    }

    public function getPostBb2020(): ?bool
    {
        return $this->postBb2020;
    }

    public function isPostBb2020(): ?bool
    {
        return $this->getPostBb2020();
    }

    public function setPostBb2020(bool $postBb2020): self
    {
        $this->postBb2020 = $postBb2020;

        return $this;
    }

    public function getReadOnly(): ?bool
    {
        return $this->readOnly;
    }

    public function isReadOnly(): ?bool
    {
        return $this->getReadOnly();
    }

    public function setReadOnly(bool $readOnly): self
    {
        $this->readOnly = $readOnly;

        return $this;
    }

    /**
     * @return Collection|Team[]
     * @Ignore()
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->setRule($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
            // set the owning side to null (unless already changed)
            if ($team->getRule() === $this) {
                $team->setRule(null);
            }
        }

        return $this;
    }

    public function __toString(): ?string
    {
        return $this->ruleKey;
    }

    /**
     * Methods
     */

    /**
     * Construct Injury Table
     *
     * @param array $rule
     *
     */
    protected function constructInjuryTable($rule)
    {
        foreach ($rule['injuries'] as $injuryKey => $injury) {
            if (isset($injury['from']) && isset($injury['to'])) {
                for ($key = $injury['from']; $key <= $injury['to']; $key++) {
                    $this->injuryTable[$key] = $injuryKey;
                }
            } elseif (isset($injury['from'])) {
                $this->injuryTable[$injury['from']] = $injuryKey;
            }
        }
    }

    /**
     * Get Experience Level For Experience Value
     *
     * @param integer $experience
     *
     * @return string|boolean
     */
    public function getExperienceLevelForValue($experience)
    {
        $datas = $this->getRule();
        ksort($datas['experience']);
        $last = false;
        foreach ($datas['experience'] as $key => $level) {
            if ($experience >= $key) {
                $last = $level;
            }
        }
        return $last;
    }

    /**
     * Get Injury For Value
     *
     * @param integer $value
     *
     * @return array|boolean
     */
    public function getInjury($value)
    {
        return (isset($this->injuryTable[$value])) ? array(
            'key_name' => $this->injuryTable[$value],
            'effect' => $this->getInjuryEffect($this->injuryTable[$value])
        ) : false ;
    }

    /**
     * Get Injury Effect For Injury Key Name
     *
     * @param string $keyName
     *
     * @return array|boolean
     */
    public function getInjuryEffect($keyName)
    {
        $datas = $this->getRule();
        return ($datas['injuries'][$keyName]) ? $datas['injuries'][$keyName]['effects'] : false;
    }
}
