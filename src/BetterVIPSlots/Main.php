<?php

namespace BetterVIPSlots;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\Config;	
	
class Main extends PluginBase implements Listener{
    public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		
		$this->loadVIPSList();
		
		$this->getLogger()->info("VIPSlots Enabled!");
	}
    
    public function onDisable(){
		$this->getLogger()->info("VIPSlots Disabled!");
    }
    
        public function onJoinEvent(PlayerJoinEvent $event){
      if($this->vips->exists(strtolower($event->getPlayer()->getName()))){
      if(count($this->getServer()->getOnlinePlayers) >= 25){
        return true;
        }
        }else{
        $player = $event->getPlayer();
        $player->kick("§4IF YOU WANT TO JOIN WHEN\n§4THE SERVER IS FULL BUY\n§4A RANK AT\n§awww.shop.pluspe.net");
        
      }
      }
    
	public function onCommand(CommandSender $p, Command $command, $label, array $args){
	
		if($command->getName() == "vips"){
		
			if(!isset($args[0]) || count($args) > 2){
				$p->sendMessage("Usage: /vips <add/remove/list>");
				return true;
			}
			
			switch(strtolower($args[0]))
			{
				case "add":
				
					if(isset($args[1])){
						$who_player = $this->getValidPlayer($args[1]);
						
						if($who_player instanceof Player){
							$target = $who_player->getName();
						}
						else{
							$target = $args[1];
						}
						
						if($this->addPlayer($target)){
							$p->sendMessage("Successfully added '$target' on VIPSlots!");
						}
						else{
							$p->sendMessage("$target is already added on VIPSlots!");
						}
					}
					else{
						$p->sendMessage("Usage: /vips add <player>");
					}
				
					break;
					
				case "remove":
				
					if(isset($args[1])){
						$who_player = $this->getValidPlayer($args[1]);
						
						if($who_player instanceof Player){
							$target = $who_player->getName();
						}
						else{
							$target = $args[1];
						}
						
						if($this->removePlayer($target)){
							$p->sendMessage("Successfully removed '$target' on VIPSlots!");
						}
						else{
							$p->sendMessage("$target doesn't exist on VIPSlots!");
						}
					}
					else{
						$p->sendMessage("Usage: /vips remove <player>");
					}
					
					break;
					
				case "list":
				
					$file = fopen($this->getDataFolder() . "vip_players.txt", "r");
					$i = 0;
					while (!feof($file)) {
						$vips[] = fgets($file);
					}
					fclose($file);
					$p->sendMessage("-==[ VIPSlots List ]==-");
					foreach ($vips as $vip){
						$p->sendMessage(" - " . $vip);
					}
				
					break;
					
				default:
				
					$p->sendMessage("Usage: /vips <add/remove/list>");
					
					break;
					
			}
			
			return true;
		}
	}
	
	/*****************
	*================*
	*==[ Non-APIs ]==*
	*================*
	*****************/
	
	private function loadVIPSList(){
		@mkdir($this->getDataFolder(), 0777, true);
		$this->vips = new Config($this->getDataFolder() . "vip_players.txt", Config::ENUM, array(
		));
	}
	
	private function getValidPlayer($target){
		$player = $this->getServer()->getPlayer($target);
		return $player instanceof Player ? $player : $this->getServer()->getOfflinePlayer($target);
	}
	
	/*****************
	*================*
	*==[   APIs   ]==*
	*================*
	*****************/
	
	public function addPlayer($player){
		$target = $this->getValidPlayer($player);
		
		if($target instanceof Player){
			$p = strtolower($target->getName());
		}
		else{
			$p = strtolower($player);
		}
		
		if($this->vips->exists($p)) return false;
		
		$this->vips->set($p, true);
		$this->vips->save();
			
		return true;
	}
	
	public function removePlayer($player){
	
		$target = $this->getValidPlayer($player);
		
		if($target instanceof Player){
			$p = strtolower($target->getName());
		}
		else{
			$p = strtolower($player);
		}
	
		if(!$this->vips->exists($p)) return false;
		
		$this->vips->remove($p);
		$this->vips->save();
		
		return true;
	}
}
