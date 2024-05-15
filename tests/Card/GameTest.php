<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Test class for class Game
 */
class GameTest extends TestCase {
    
    private $game;
    private $session;
    
    protected function setUp(): void
    {
        $this->game = new Game();
        $this->session = new Session(new MockArraySessionStorage());
        $this->game->initGame($this->session);
    }

    public function testCreateGameObject() {
        $this->assertInstanceOf("\App\Card\Game", $this->game);
    }

    public function testGameInit() {
        // $this->game->initGame($this->session);
        $this->assertInstanceOf("\App\Card\Deck", $this->session->get("deck"));
        $this->assertEquals($this->session->get("cards"), []);
        $this->assertEquals($this->session->get("bank"), []);
        $this->assertEquals($this->session->get("player_score"), 0);
        $this->assertEquals($this->session->get("bank_score"), 0);
        $this->assertFalse($this->session->get("finished"));
    }

    public function testGameGetScore() {
        $this->assertEquals($this->game->getPlayerScore(), 0);
        $this->assertEquals($this->game->getBankScore(), 0);
    }

    public function testPlayerDraw() {

        $this->game->playerDraw($this->session);
        $this->assertNotEquals($this->session->get("player_score"), 0);
        $this->assertEquals($this->session->get("deck")->getSizeOfDeck(), 51);
    }

    public function testBankDraw() {

        $this->game->playerDraw($this->session);
        $this->game->bankDraw($this->session);
        $this->assertNotEquals($this->session->get("bank_score"), 0);
        $this->assertNotEquals($this->session->get("deck")->getSizeOfDeck(), 52);
        $this->assertNotEquals($this->session->get("cards"), []);
        $this->assertNotEquals($this->session->get("bank"), []);
    }

    public function testPlayerFat() {
        $this->session->set("ace", 1);
        $this->session->set("player_score", 22);
        $this->game->playerDraw($this->session);
        $this->assertEquals($this->session->get("ace"), 0);
        $this->assertEquals($this->session->get("player_score"), 12);
    }

}
