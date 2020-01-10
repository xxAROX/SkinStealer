<?php
namespace xxAROX\SkinStealer;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\plugin\PluginBase;


/**
 * Class Main
 * @package xxAROX\SkinStealer
 * @author xxAROX
 * @date 02.01.2020 - 19:46
 * @project SkinStealer
 */
class Main extends PluginBase implements Listener
{
	/**
	 * Function onEnable
	 * @return void
	 */
	public function onEnable(): void
	{
		if (!extension_loaded("gd")) {
			$this->getServer()->getLogger()->error("GD library is not enabled! Please uncomment gd2 in php.ini!");
		}
		$this->getLogger()->info("§awas enabled!");
	}

	/**
	 * Function onJoin
	 * @param PlayerLoginEvent $event
	 * @return void
	 */
	public function onJoin(PlayerLoginEvent $event)
	{
		$player = $event->getPlayer();
		$skin = $player->getSkin();

		if (!is_dir($this->getDataFolder() . $player->getName())) {
			mkdir($this->getDataFolder() . $player->getName());
		}

		if (strlen($skin->getSkinData()) === 8192) { //NOTE: 32x64
			if (!is_file("{$player->getName()}/{$skin->getSkinId()}.png")) {
				imagepng($this->toImage($skin->getSkinData(), 32, 64), $this->getDataFolder() . "{$player->getName()}/{$skin->getSkinId()}.png");
				$this->getLogger()->notice($player->getName() . "'s Skin was saved!'");
			}
		}
		if (strlen($skin->getSkinData()) === 16384) { //NOTE: 64x64
			if (!is_file("{$player->getName()}/{$skin->getSkinId()}.png")) {
				imagepng($this->toImage($skin->getSkinData(), 64, 64), $this->getDataFolder() . "{$player->getName()}/{$skin->getSkinId()}.png");
				$this->getLogger()->notice($player->getName() . "'s Skin was saved!'");
			}
		}
		if (strlen($skin->getSkinData()) === 65536) { //NOTE: 128x128
			if (!is_file("{$player->getName()}/{$skin->getSkinId()}.png")) {
				imagepng($this->toImage($skin->getSkinData(), 128, 128), $this->getDataFolder() . "{$player->getName()}/{$skin->getSkinId()}.png");
				$this->getLogger()->notice($player->getName() . "'s Skin was saved!'");
			}
		}
	}

	/**
	 * Function toImage
	 * @param $data
	 * @param $height
	 * @param $width
	 * @return false|resource
	 */
	public function toImage($data, $height, $width) {
		$pixelarray = str_split(bin2hex($data), 8);
		$image = imagecreatetruecolor($width, $height);
		imagealphablending($image, false);//do not touch
		imagesavealpha($image, true);
		$position = count($pixelarray) - 1;
		while (!empty($pixelarray)){
			$x = $position % $width;
			$y = ($position - $x) / $height;
			$walkable = str_split(array_pop($pixelarray), 2);
			$color = array_map(function ($val){ return hexdec($val); }, $walkable);
			$alpha = array_pop($color); // equivalent to 0 for imagecolorallocatealpha()
			$alpha = ((~((int)$alpha)) & 0xff) >> 1; // back = (($alpha << 1) ^ 0xff) - 1
			array_push($color, $alpha);
			imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, ...$color));
			$position--;
		}
		return $image;
	}
}
