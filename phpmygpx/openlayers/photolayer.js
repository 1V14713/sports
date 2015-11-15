/**
* @version $Id: photolayer.js 436 2012-06-29 19:34:13Z eska $
* @package phpmygpx
* @copyright Copyright (C) 2010-2012 Florian Lohoff, Sebastian Klemm
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

PhotoLayer = new OpenLayers.Class({

	url: null,
	layer: null,
	parser: null,
	request: null,
	clicked: null,
	current: null,
	minzoom: 14,

	initialize: function(url, map, options) {
		this.url = url;
		this.map = map;

		OpenLayers.Util.extend(this, options);

		this.parser = new OpenLayers.Format.JSON();
		this.layer = new OpenLayers.Layer.Markers("Photos");

		this.layer.events.register("visibilitychanged", this, this.refresh);
		this.layer.events.register("moveend", this, this.refresh);

		this.map.addLayer(this.layer);

		this.photos = new PhotoLayer.Photos(map, this.layer);
	},

	onSuccess: function(result) {

		if (result.responseText.search(/^error/) != -1) {
			OpenLayers.Console.error("Data returned by server contains error: " + result.responseText);
			this.onFailure(result);
			return;
		}

		this.layer.clearMarkers();

		var photos = this.parser.read(result.responseText);

		if (photos && photos[0]) {
			for (var i = 0; i < photos.length; i++) {
				this.photos.addphoto(photos[i].lon, photos[i].lat, photos[i].photoid, photos[i].time, photos[i].viewdir, photos[i].width, photos[i].height);
			}
		}

		document.getElementById('spinning').style.display="none";

		this.request = null;
	},

	onFailure: function(result) {
		OpenLayers.Console.error("Failed to fetch image data: " + result);
		document.getElementById('spinning').style.display="none";
	},

	refresh: function(params) {
		var zoom = this.map.getZoom ();
		var visible = this.layer.getVisibility();

		if (this.request) {
			/* If request is running abort it */
			this.request.transport.abort();
		}

		if (zoom < this.minzoom || !visible) {
			this.layer.clearMarkers();
			return;
		}

		document.getElementById('spinning').style.display="block";

		var bounds = this.map.getExtent().toArray();

		this.request = new OpenLayers.Ajax.Request(this.url, {
			method: "get",
			parameters: {
				'b': y2lat(bounds[1]),
				't': y2lat(bounds[3]),
				'l': x2lon(bounds[0]),
				'r': x2lon(bounds[2]),
				'task': 'getPhotos',
				'zoom': zoom,
				'data': this.data
			},
		onSuccess: OpenLayers.Function.bind(this.onSuccess, this),
		onFailure: OpenLayers.Function.bind(this.onFailure, this)
		});
	}
});

PhotoLayer.Photos = new OpenLayers.Class({
	photolayer: null,
	lonlat: null,
	icon: null,
	icons_direction: null,
	feature: null,
	popup: null,

	layer: null,
	map: null,

	width: null,
	height: null,

	initialize: function(map, layer) {
		this.map = map;
		this.layer = layer;

		var size = new OpenLayers.Size(24,24);
		var offset = new OpenLayers.Pixel(-(size.w/2), -(size.h/2));
		this.icon = new OpenLayers.Icon ('images/camera1.png', size, offset);
		
		var size2 = new OpenLayers.Size(69,69);
		var offset2 = new OpenLayers.Pixel(-(size2.w/2), -(size2.h/2));
		this.icons_direction = Array(
			new OpenLayers.Icon ('images/camera_000.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_015.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_030.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_045.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_060.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_075.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_090.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_105.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_120.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_135.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_150.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_165.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_180.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_195.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_210.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_225.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_240.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_255.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_270.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_285.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_300.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_315.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_330.gif', size2, offset2),
			new OpenLayers.Icon ('images/camera_345.gif', size2, offset2) );
	},

	addphoto: function(lon, lat, id, time, viewdir, width, height) {
		var lonlat = new OpenLayers.LonLat(lon, lat);
		var zoom = this.map.getZoom ();
		var feature;
		
		if(!viewdir || zoom<16) {
			feature = new OpenLayers.Feature(this.layer, lonlat, {icon: this.icon.clone() });
		} else {
			var viewdir_icon = this.icons_direction[Math.floor((parseFloat(viewdir)+7.5)/15)];
			feature = new OpenLayers.Feature(this.layer, lonlat, {icon: viewdir_icon.clone() });
		}
		feature.closeBox = false;
		feature.photoid = id;
		feature.width = width;
		feature.height = height;
		feature.popupClass = OpenLayers.Class(OpenLayers.Popup.FramedCloud, {
			panMapIfOutOfView: true,
			autoSize: true
		} );
		feature.data.popupContentHTML = '<img src="getphoto.php?x=0&id=' + id + '" />' +
			'<br>' + time;
		feature.photos = this;

		var marker = feature.createMarker();
		marker.events.register("mouseover", feature, this.mouseover);
		marker.events.register("mousedown", feature, this.mouseclick);
		marker.events.register("mouseout", feature, this.mouseout);
		this.layer.addMarker(marker);
	},

	showpopup: function(photos, feature) {
		if (photos.current != null)
			photos.current.hide();
		if (feature.popup == null) {
			feature.popup = feature.createPopup();
			photos.map.addPopup(feature.popup);
		} else {
			feature.popup.toggle ();
		}
		photos.current = feature.popup;
	},

	mouseout: function(evt) {
		if (this.photos.current)
			this.photos.current.hide();
		OpenLayers.Event.stop(evt);
	},

	mouseover: function(evt) {
		this.photos.showpopup(this.photos, this);
		OpenLayers.Event.stop(evt);
	},

	mouseclick: function(evt) {
		window.open ("photos.php?task=details&id=" + this.photoid + "&width=" + this.width + "&height=" + this.height); 
		OpenLayers.Event.stop(evt);
	}
});
