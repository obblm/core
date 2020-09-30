<?php

namespace Obblm\Core\Tests\Helper;

use Obblm\Core\Helper\CoreTranslation;
use PHPUnit\Framework\TestCase;

class TranslatorTest extends TestCase
{
    const RULE = 'ruleKey';
    const FIELD = 'fieldKey';
    const WEATHER = 'weatherKey';
    const ROSTER = 'rosterKey';
    const SKILL = 'skillKey';
    const SKILL_TYPE = 'skillTypeKey';
    public function testAdd()
    {
        $this->getRule();
        $this->getRoster();
        $this->getSkill();
    }
    private function getRule() {
        $expected = self::RULE .'.title';
        $result = CoreTranslation::getRuleTitle(self::RULE);
        $this->assertSame($expected, $result);
        $expected = self::RULE .'.fields.' . self::FIELD . '.title';
        $result = CoreTranslation::getFieldKey(self::RULE, self::FIELD);
        $this->assertSame($expected, $result);
        $expected = self::RULE .'.fields.' . self::FIELD . '.weather.' . self::WEATHER;
        $result = CoreTranslation::getWeatherKey(self::RULE, self::FIELD, self::WEATHER);
        $this->assertSame($expected, $result);
    }
    private function getRoster() {
        $expected = self::RULE . '.rosters.' . self::ROSTER .'.title';
        $result = CoreTranslation::getRosterKey(self::RULE, self::ROSTER);
        $this->assertSame($expected, $result);
        $expected = self::RULE . '.rosters.' . self::ROSTER .'.description';
        $result = CoreTranslation::getRosterDescription(self::RULE, self::ROSTER);
        $this->assertSame($expected, $result);
    }
    private function getSkill() {
        $expected = self::RULE . '.skills.' . self::SKILL .'.title';
        $result = CoreTranslation::getSkillNameKey(self::RULE, self::SKILL);
        $this->assertSame($expected, $result);
        $expected = self::RULE . '.skills.' . self::SKILL .'.description';
        $result = CoreTranslation::getSkillDescription(self::RULE, self::SKILL);
        $this->assertSame($expected, $result);
        $expected = self::RULE . '.skill_types.' . self::SKILL_TYPE;
        $result = CoreTranslation::getSkillType(self::RULE, self::SKILL_TYPE);
        $this->assertSame($expected, $result);
    }
    private function getTeam() {
        $expected = self::RULE . '.rosters.' . self::ROSTER .'.title';
        $result = CoreTranslation::getRosterKey(self::RULE, self::ROSTER);
        $this->assertSame($expected, $result);
    }
    private function getPlayer() {
        $expected = self::RULE . '.rosters.' . self::ROSTER .'.title';
        $result = CoreTranslation::getRosterKey(self::RULE, self::ROSTER);
        $this->assertSame($expected, $result);
    }
}