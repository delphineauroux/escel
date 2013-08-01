// initialisation
soundManager.setup
({
	url: "external_SoundManager/swf/soundmanager2.swf",
	preferFlash: false,
	useHTML5Audio: true,
	waitForWindowLoad: true
});

Son = function(id_son, location_son)
{
	this.id = id_son;
	this.location_son = location_son;
	soundManager.createSound({
								id: id_son,
								url: location_son,
								autoLoad: true,
								autoPlay: false,
								pan: 0,
								volume: 100
							});
	this.play = function()
	{
		soundManager.play(this.id);
		soundManager.setPosition(this.id, 0);
	}
	this.destroy = function()
	{soundManager.destroySound(this.id);}
}


