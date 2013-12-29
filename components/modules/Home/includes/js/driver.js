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
    var container, driver_map;
    driver_map = $('#driver-map');
    if (!driver_map.length) {
      return;
    }
    (function(w) {
      return driver_map.width(w).css({
        marginLeft: 500 - w / 2
      });
    })($(window).width());
    container = $('.home-page-filter');
    container.find('input[name=date]').pickmeup({
      format: 'd.m.Y',
      change: function(formated) {
        return container.find('[name=date]').val(formated).pickmeup('hide').change();
      }
    });
    container.find('[name=time]').next().find('a').click(function() {
      return container.find('[name=time]').val($(this).text()).change();
    });
    return ymaps.ready(function() {
      var add_destination, find_goods, map, search_timeout;
      map = new ymaps.Map('driver-map', {
        center: [50.4505, 30.523],
        zoom: 13,
        controls: ['geolocationControl', 'fullscreenControl', 'typeSelector', 'zoomControl']
      });
      map.behaviors.disable('scrollZoom');
      driver_map.get(0).close_balloon = function() {
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
          hintContent: 'Книжковий магазин Свічадо'
        }, {
          iconLayout: 'default#image',
          iconImageHref: '/components/modules/Home/includes/img/destination.png',
          iconImageSize: [60, 58],
          iconImageOffset: [-24, -58],
          balloonLayout: ymaps.templateLayoutFactory.createClass("<section class=\"home-page-map-balloon-container centers\">\n	<header><h1>Книжковий магазин Свічадо</h1> <a class=\"uk-close\" onclick=\"$('#driver-map').get(0).close_balloon()\"></a></header>\n	<article>\n		<address>вулиця Покровська, 6</address>\n		<time>Будні: з 10:00 до 17:00</time>\n	</article>\n</section>")
        }));
      };
      add_destination();
      find_goods = function() {
        $.ajax({
          url: 'api/Home/find_goods',
          data: {
            date: container.find('input[name=date]').val(),
            time: container.find('[name=time]').val(),
            reserved: $('.home-page-map-switcher .uk-active input').val() === 'reserved_goods' ? 1 : 0
          },
          type: 'get',
          success: function(result) {
            var admin, good, icon_number, lat, lng, reservation, _i, _len, _results;
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
                reservation = driver_id === parseInt(good.reserved_driver, 10) ? "<button class=\"reservation uk-button\" data-id=\"" + good.id + "\" disabled>Зарезервовано</button>" : "<button class=\"reservation uk-button\" data-id=\"" + good.id + "\">Заберу за 24 години</button>";
                admin = window.cs.is_admin ? "<span class=\"uk-icon-trash delete-good\" data-id=\"" + good.id + "\"></span>" : '';
                _results.push(map.geoObjects.add(new ymaps.Placemark([good.lat, good.lng], {
                  hintContent: good.username + ' ' + good.phone
                }, {
                  iconLayout: 'default#image',
                  iconImageHref: '/components/modules/Home/includes/img/map-icons.png',
                  iconImageSize: [60, 58],
                  iconImageOffset: [-24, -58],
                  iconImageClipRect: [[60 * icon_number, 0], [60 * (icon_number + 1), 58]],
                  balloonLayout: ymaps.templateLayoutFactory.createClass("<section class=\"home-page-map-balloon-container\">\n	<header><h1>" + good.username + " <small>" + good.phone + "</small></h1> " + admin + "<a class=\"uk-close\" onclick=\"$('#driver-map').get(0).close_balloon()\"></a></header>\n	<article>\n		<address>" + good.address + "</address>\n		<time>" + good.date + " (" + good.time + ")</time>\n		<p>" + good.comment + "</p>\n	</article>\n	<footer>" + reservation + "</footer>\n</section>")
                })));
              }
              return _results;
            }
          }
        });
        return container.on('click', '.reservation', function() {
          var reservation;
          reservation = $(this);
          return $.ajax({
            url: 'api/Home/reservation',
            data: {
              id: reservation.data('id')
            },
            success: function() {
              reservation.html('Зарезервовано').prop('disabled', true);
              alert('Прийнято! Дякуємо та чекаємо вашого приїзду!');
              return find_goods();
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
      };
      find_goods();
      search_timeout = 0;
      container.on('keyup change', '[name=date], [name=time], .home-page-map-switcher', function() {
        clearTimeout(search_timeout);
        return search_timeout = setTimeout(find_goods, 300);
      });
      return driver_map.on('click', '.delete-good', function() {
        if (!window.cs.is_admin) {
          return;
        }
        if (!confirm('Точно видалити?')) {
          return;
        }
        return $.ajax({
          url: 'api/Home/delete_good',
          data: {
            id: $(this).data('id')
          },
          success: function() {
            return find_goods();
          }
        });
      });
    });
  });

}).call(this);
