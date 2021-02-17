<?php

namespace HannesTheDev\MineraMoneyDrop;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;

class Main extends PluginBase
{

    const PREFIX = "§8[§6§lMoneyDrop§r§8] ";

    public function onEnable()
    {
        $plugin = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
        if (is_null($plugin)) {
            $this->getLogger()->info("You must installing EconomyAPI!");
            $this->getServer()->shutdown();
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool
    {
        switch ($command->getName()) {
            case "moneydrop":
                if ($sender instanceof Player) {
                    $eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
                    if (isset($args[0])) {
                        if (is_numeric($args[0])) {
                            $amount = $args[0];
                            $op = count($this->getServer()->getOnlinePlayers());
                            $sendermoney = $eco->myMoney($sender);
                            $result2 = $amount / $sendermoney;
                            if ($amount >= 5000) {
                                if ($sendermoney >= $amount) {
                                    if ($op >= 5) {
                                        $result = $amount / $op;
                                        $players = [];
                                        foreach ($this->getServer()->getOnlinePlayers() as $online) {
                                            $players[] = $online->getName();
                                        }
                                        $names = implode(", ", $players);
                                        $eco->reduceMoney($sender, $amount);
                                        $this->getServer()->broadcastMessage(Main::PREFIX . "§6The Players: §e\n" . $names . "\n§3Become §a§l" . $result . " Coins §r§3.\n" . Main::PREFIX . "§3Very thanks §c" . $sender->getName() . " §3for your spend of §a" . $amount . "x Coins§3 ! §c<3");
                                        if (!$online->isOp()) {
                                            if ($online !== $sender) {
                                                $eco->addMoney($online, $result);
                                            }
                                        }
                                    } else {
                                        $sender->sendMessage(Main::PREFIX . "§cThere are too few players online!");
                                    }
                                } else {
                                    $sender->sendMessage(Main::PREFIX . "§cYou need §l" . $result2 . " Coins §r§cto make a money drop!");
                                }
                            } else {
                                $sender->sendMessage(Main::PREFIX . "§cYou can't make a money drop under §l5000 Coins§r§c!");
                            }
                        } else {
                            $sender->sendMessage(Main::PREFIX . "§cYou must enter a Number!");
                        }
                    } else {
                        $sendermoney = $eco->myMoney($sender);
                        $sender->sendMessage(Main::PREFIX . "§3This command is used to 'distribute' §2Coins §3to each player. Also has its §econditions§3:\n§3 - §emin. 5000 Coins§3, §e5 online players§3, §eexcluded OP-Teamlers and yourself\n§6[Usage] '§3/moneydrop 5000§8' §eby 5 other Players = §aEveryone became 1000 Coins\n\n" . Main::PREFIX . "§3You have §2" . $sendermoney . " Coins");
                    }
                } else {
                    $sender->sendMessage(Main::PREFIX . "§cYou must be a player to use /moneydrop!");
                }
                break;
        }
        return true;
    }
}