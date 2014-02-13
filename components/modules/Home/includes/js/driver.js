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
    var container;
    if (!$('#map').length) {
      return;
    }
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
      var clusterer, find_goods, search_timeout;
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          return map.panTo([position.coords.latitude, position.coords.longitude]);
        }, function() {}, {
          enableHighAccuracy: true,
          timeout: 30 * 60 * 1000
        });
      }
      clusterer = new ymaps.Clusterer();
      map.geoObjects.add(clusterer);
      find_goods = function() {
        return $.ajax({
          url: 'api/Home/find_goods',
          data: {
            date: container.find('input[name=date]').val(),
            time: container.find('[name=time]').val(),
            reserved: $('.home-page-map-switcher.driver .uk-active input').val() === 'reserved_goods' ? 1 : 0
          },
          type: 'get',
          success: function(result) {
            var admin, good, icon_number, lat, lng, placemarks, reservation, _i, _len;
            if (result && result.length) {
              lat = [0, 0];
              lng = [0, 0];
              placemarks = [];
              for (_i = 0, _len = result.length; _i < _len; _i++) {
                good = result[_i];
                lat = [Math.min(lat[0], good.lat), Math.max(lat[0], good.lat)];
                lng = [Math.min(lng[0], good.lng), Math.max(lng[0], good.lng)];
                icon_number = Math.round(Math.random() * 11);
                reservation = driver_id === parseInt(good.reserved_driver, 10) && good.reserved > (new Date).getTime() / 1000 ? "<button class=\"reserved uk-button\" data-id=\"" + good.id + "\">Зарезервовано</button>" : "<button class=\"reservation uk-button\" data-id=\"" + good.id + "\">Заберу за 24 години</button>";
                admin = window.cs.is_admin ? "<span class=\"uk-icon-trash delete-good\" data-id=\"" + good.id + "\"></span>" : '';
                placemarks.push(new ymaps.Placemark([good.lat, good.lng], {
                  hintContent: good.username + ' ' + good.phone
                }, {
                  iconLayout: 'default#image',
                  iconImageHref: '/components/modules/Home/includes/img/map-icons.png',
                  iconImageSize: [60, 58],
                  iconImageOffset: [-24, -58],
                  iconImageClipRect: [[60 * icon_number, 0], [60 * (icon_number + 1), 58]],
                  iconImageShape: map.icons_shape,
                  balloonLayout: ymaps.templateLayoutFactory.createClass("<section class=\"home-page-map-balloon-container\">\n	<header><h1>" + good.username + " <small>" + good.phone + "</small></h1> " + admin + "<a class=\"uk-close\" onclick=\"map.balloon.close()\"></a></header>\n	<article>\n		<address>" + good.address + "</address>\n		<time>" + good.date + " (" + good.time + ")</time>\n		<p>" + good.comment + "</p>\n	</article>\n	<footer>" + reservation + "</footer>\n</section>")
                }));
              }
              clusterer.removeAll();
              return clusterer.add(placemarks);
            }
          }
        });
      };
      container.on('click', '.reservation', function() {
        var reservation;
        reservation = $(this);
        return $.ajax({
          url: 'api/Home/reservation',
          type: 'post',
          data: {
            id: reservation.data('id')
          },
          success: function() {
            reservation.html('Зарезервовано').removeClass('reservation').addClass('reserved');
            alert('Зарезервовано! Дякуємо та чекаємо вашого приїзду!');
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
      }).on('click', '.reserved', function() {
        var reserved;
        reserved = $(this);
        return $.ajax({
          url: 'api/Home/reservation',
          type: 'delete',
          data: {
            id: reserved.data('id')
          },
          success: function() {
            reserved.html('Заберу за 24 години').removeClass('reserved').addClass('reservation');
            alert('Резерв скасовано! Дякуємо що попередили!');
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
      find_goods();
      search_timeout = 0;
      container.on('keyup change', '[name=date], [name=time], .home-page-map-switcher.driver input', function() {
        clearTimeout(search_timeout);
        return search_timeout = setTimeout(find_goods, 300);
      });
      $('.home-page-map-switcher.driver').on('keyup change', 'input', function() {
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
