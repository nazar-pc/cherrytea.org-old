// Generated by CoffeeScript 1.4.0

/*
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
*/


(function() {

  $(function() {
    var container, coordinates;
    if (!$('#giver-map').length) {
      return;
    }
    container = $('.home-page-add-goods');
    container.find('[name=date]').pickmeup({
      format: 'd.m.Y',
      mode: 'range',
      onChange: function(formated) {
        return container.find('[name=date]').val(formated.join(' - '));
      }
    });
    container.find('[name=time]').next().find('a').click(function() {
      return container.find('[name=time]').val($(this).text());
    });
    coordinates = container.find('[name=coordinates]');
    return ymaps.ready(function() {
      var address_timeout, icon_number, map, me;
      map = new ymaps.Map('giver-map', {
        center: cs.json_decode(coordinates.val()),
        zoom: 13,
        controls: ['zoomControl']
      });
      icon_number = Math.round(Math.random() * 11);
      me = new ymaps.Placemark(cs.json_decode(coordinates.val()), {}, {
        draggable: true,
        iconLayout: 'default#image',
        iconImageHref: '/components/modules/Home/includes/img/map-icons.png',
        iconImageSize: [60, 58],
        iconImageOffset: [-24, -58],
        iconImageClipRect: [[60 * icon_number, 0], [60 * (icon_number + 1), 58]]
      });
      map.geoObjects.add(me);
      me.events.add('geometrychange', function(e) {
        var coords;
        coords = cs.json_encode(e.get('originalEvent').originalEvent.newPosition);
        return coordinates.val(coords);
      });
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          var coords;
          coords = [position.coords.latitude, position.coords.longitude];
          map.panTo(coords);
          return me.geometry.setCoordinates(coords);
        }, function() {}, {
          enableHighAccuracy: true,
          timeout: 120 * 1000
        });
        address_timeout = 0;
        return container.find('[name=address]').on('keyup change', function() {
          if ($(this).val().length < 4) {
            return;
          }
          clearTimeout(address_timeout);
          return address_timeout = setTimeout((function() {
            return ymaps.geocode(container.find('[name=address]').val()).then(function(res) {
              var coords;
              coords = res.geoObjects.get(0).geometry.getCoordinates();
              map.panTo(coords, {
                fly: true,
                checkZoomRange: true
              });
              me.geometry.setCoordinates(coords);
              return coordinates.val(cs.json_encode(coords));
            });
          }), 300);
        });
      }
    });
  });

}).call(this);
