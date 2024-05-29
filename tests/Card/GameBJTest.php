<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Test class for class Game
 */
class GameBJTest extends TestCase {
    
    private $game;
    private $session;
    
    protected function setUp(): void
    {
        $this->game = new GameBJ();
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
        $this->assertEquals($this->session->get("deck")->getSizeOfDeck(), 4 * 52 - 1);
    }

    public function testBankDraw() {

        $this->game->playerDraw($this->session);
        $this->game->bankDraw($this->session);
        $this->assertNotEquals($this->session->get("bank_score"), 0);
        $this->assertNotEquals($this->session->get("deck")->getSizeOfDeck(), 4 * 52);
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

    public function testUpdateSum() {
        $this->assertEquals($this->game->updateSum("A", $this->session), 11);
        $this->assertEquals($this->game->updateSum("K", $this->session), 10);
    }

    public function testDecreaseAce() {
        $this->session->set("ace", 1);
        $this->assertEquals($this->game->decreaseAce($this->session), -10);
        $this->assertEquals($this->game->decreaseAce($this->session), 0);
    }

    public function testPlayerDrawFinished() {
        $this->session->set("player_score", 22);
        $this->game->playerDraw($this->session);
        $this->assertEquals($this->session->get("player_score"), 0);
    }

    public function testPlayerDrawFinished2() {
        $this->session->set("active", 1);
        $this->session->set("num_hands", 1);
        $this->game->playerDraw($this->session);
        $this->assertTrue($this->session->get("finished"));
    }

    public function testPlayerDrawHandHasCards() {
        $this->game->playerDraw($this->session);
        $this->game->playerDraw($this->session);
        $hands = $this->session->get("hands");
        $this->assertEquals(count($hands[0]), 2);
    }

    public function testPlayerDrawHandHasCardsWithAce() {
        $this->game->playerDraw($this->session);
        $this->session->set("ace", 1);
        $this->session->set("player_score", 22);
        $this->game->playerDraw($this->session);
        $this->assertFalse($this->session->get("finished"));
    }

    // public function testBankDrawUntilAce() {
    //     $this->session->set("player_score", 21);
    //     $this->session->set("score", [21]);
    //     $dum = true;
    //     while ($dum) {
    //         $this->game->bankDraw($this->session);
    //         $bank = implode($this->session->get("bank"));
    //         if (str_contains($bank, "A")) {
    //             $dum = false;
    //         }
    //     }
    //     $this->assertFalse($dum);
    // }
}
