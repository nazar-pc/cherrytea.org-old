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
    var guest_map;
    $('.home-page-sign-in a').click(function() {
      var $this, provider;
      $this = $(this);
      provider = $this.hasClass('fb') ? 'Facebook' : 'Vkontakte';
      return $.ajax({
        url: 'api/Home/i_am_driver',
        data: {
          driver: $this.hasClass('driver') ? 1 : 0
        },
        success: function() {
          return location.href = 'HybridAuth/' + provider;
        },
        error: function(xhr) {
          if (xhr.responseText) {
            return alert(cs.json_decode(xhr.responseText).error_description);
          } else {
            return alert(L.auth_connection_error);
          }
        }
      });
    });
    guest_map = $('#guest-map');
    if (!guest_map.length) {
      return;
    }
    (function(w) {
      return guest_map.width(w).css({
        marginLeft: 500 - w / 2
      });
    })($(window).width());
    return ymaps.ready(function() {
      var add_destination, find_givers, map;
      map = new ymaps.Map('guest-map', {
        center: [50.4505, 30.523],
        zoom: 13,
        controls: ['geolocationControl', 'fullscreenControl', 'typeSelector', 'zoomControl']
      });
      guest_map.get(0).close_balloon = function() {
        return map.balloon.close();
      };
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          return map.panTo([position.coords.latitude, position.coords.longitude]);
        }, function() {}, {
          enableHighAccuracy: true,
          timeout: 30 * 60 * 1000
        });
      }
      add_destination = function() {
        map.geoObjects.add(new ymaps.Placemark([50.487124, 30.596273], {
          hintContent: 'Благодійний фонд Карітас-Київ'
        }, {
          iconLayout: 'default#image',
          iconImageHref: '/components/modules/Home/includes/img/destination.png',
          iconImageSize: [60, 58],
          iconImageOffset: [-24, -58],
          balloonLayout: ymaps.templateLayoutFactory.createClass("<section class=\"home-page-map-balloon-container centers\">\n	<header><h1>Благодійний фонд Карітас-Київ</h1> <a class=\"uk-close\" onclick=\"$('#driver-map').get(0).close_balloon()\"></a></header>\n	<article>\n		<address>вулиця Івана Микитенка, 7б</address>\n		<time>Будні: з 9:00 до 18:00<br>Вихідні: з 10:00 до 15:00</time>\n	</article>\n</section>")
        }));
        return map.geoObjects.add(new ymaps.Placemark([50.461404, 30.519216], {
          hintContent: 'Благодійний фонд Карітас-Київ'
        }, {
          iconLayout: 'default#image',
          iconImageHref: '/components/modules/Home/includes/img/destination.png',
          iconImageSize: [60, 58],
          iconImageOffset: [-24, -58],
          balloonLayout: ymaps.templateLayoutFactory.createClass("<section class=\"home-page-map-balloon-container centers\">\n	<header><h1>Книжковий магазин Свічадо</h1> <a class=\"uk-close\" onclick=\"$('#driver-map').get(0).close_balloon()\"></a></header>\n	<article>\n		<address>вулиця Покровська, 6</address>\n		<time>Будні: з 10:00 до 17:00</time>\n	</article>\n</section>")
        }));
      };
      add_destination();
      find_givers = function() {
        return $.ajax({
          url: 'api/Home/find_givers',
          type: 'get',
          success: function(result) {
            var good, icon_number, lat, lng, _i, _len, _results;
            map.geoObjects.removeAll();
            add_destination();
            if (result && result.length) {
              lat = [0, 0];
              lng = [0, 0];
              _results = [];
              for (_i = 0, _len = result.length; _i < _len; _i++) {
                good = result[_i];
                lat = [Math.min(lat[0], good.lat), Math.max(lat[0], good.lat)];
                lng = [Math.min(lng[0], good.lng), Math.max(lng[0], good.lng)];
                icon_number = Math.round(Math.random() * 11);
                _results.push(map.geoObjects.add(new ymaps.Placemark([good.lat, good.lng], {}, {
                  iconLayout: 'default#image',
                  iconImageHref: '/components/modules/Home/includes/img/map-icons.png',
                  iconImageSize: [60, 58],
                  iconImageOffset: [-24, -58],
                  iconImageClipRect: [[60 * icon_number, 0], [60 * (icon_number + 1), 58]]
                })));
              }
              return _results;
            }
          }
        });
      };
      return find_givers();
    });
  });

}).call(this);
