<?php


namespace syouyu\sintyoku;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Config;

class Main extends PluginBase
{
	public $config;

	public function onEnable()
	{
		$this->config = new Config($this->getDataFolder()."sintyoku.yml", Config::YAML, [
			"sintyoku" => [
				#正の数を入力してください
				180,
				500,
			]
		]);
		$config = $this->config->get("sintyoku");
		$this->getScheduler()->scheduleDelayedTask(new SintyokuTask($this), rand($config[0], $config[1]) * 20);
	}

	function repeat(){
		$config = $this->config->get("sintyoku");
		$this->getScheduler()->scheduleDelayedTask(new SintyokuTask($this), rand($config[0], $config[1]) * 20);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
	{
		switch ($command){
			case "dasimasita":
				$sender->sendMessage("その調子でもっと進捗を出してください！");
				break;
			case "damedesu":
				$sender->sendMessage("進捗出せ");
				break;
		}
		return true;
	}
}

class SintyokuTask extends Task{

	public $main;

	public function __construct(Main $main)
	{
		$this->main = $main;
	}

	public function onRun(int $currentTick)
	{
		Server::getInstance()->broadcastMessage("進捗どうですか");
		$this->main->getCommand("damedesu")->setPermission(true);
		$this->main->getCommand("dasimasita")->setPermission(true);
		$this->main->repeat();
		$this->main->getScheduler()->scheduleDelayedTask(new ClosureTask(
			function (int $currentTick):void{
				$this->main->getCommand("damedesu")->setPermission(false);
				$this->main->getCommand("dasimasita")->setPermission(false);
			}
		), 20 * 5);
	}
}