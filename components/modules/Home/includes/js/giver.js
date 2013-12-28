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
    var container, coordinates, giver_map;
    giver_map = $('#giver-map');
    if (!giver_map.length) {
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
    container.find('[name=time]').next().next().find('a').click(function() {
      return container.find('[name=time]').val($(this).text());
    });
    coordinates = container.find('[name=coordinates]');
    return ymaps.ready(function() {
      var add_destination, address_timeout, icon_number, map, me;
      map = new ymaps.Map('giver-map', {
        center: cs.json_decode(coordinates.val()),
        zoom: 13,
        controls: ['zoomControl']
      });
      document.querySelector('#giver-map').close_balloon = function() {
        return map.balloon.close();
      };
      add_destination = function() {
        map.geoObjects.add(new ymaps.Placemark([50.487124, 30.596273], {
          hintContent: 'Благодійний фонд Карітас-Київ'
        }, {
          iconLayout: 'default#image',
          iconImageHref: '/components/modules/Home/includes/img/destination.png',
          iconImageSize: [60, 58],
          iconImageOffset: [-24, -58],
          balloonLayout: ymaps.templateLayoutFactory.createClass("<section class=\"home-page-map-balloon-container\">\n	<header><h1>Благодійний фонд Карітас-Київ</h1> <a class=\"uk-close\" onclick=\"$('#giver-map').get(0).close_balloon()\"></a></header>\n	<article>\n		<address>вулиця Івана Микитенка, 7б</address>\n		<time>Будні: з 9:00 до 18:00<br>Вихідні: з 10:00 до 15:00</time>\n	</article>\n</section>")
        }));
        return map.geoObjects.add(new ymaps.Placemark([50.461404, 30.519216], {
          hintContent: 'Благодійний фонд Карітас-Київ'
        }, {
          iconLayout: 'default#image',
          iconImageHref: '/components/modules/Home/includes/img/destination.png',
          iconImageSize: [60, 58],
          iconImageOffset: [-24, -58],
          balloonLayout: ymaps.templateLayoutFactory.createClass("<section class=\"home-page-map-balloon-container\">\n	<header><h1>Книжковий магазин Свічадо</h1> <a class=\"uk-close\" onclick=\"$('#giver-map').get(0).close_balloon()\"></a></header>\n	<article>\n		<address>вулиця Покровська, 6</address>\n		<time>Будні: з 10:00 до 17:00</time>\n	</article>\n</section>")
        }));
      };
      add_destination();
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
