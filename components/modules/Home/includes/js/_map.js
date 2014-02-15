// Generated by CoffeeScript 1.4.0

/*
 * @package		Home
 * @category	modules
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2013-2014, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
*/


(function() {

  $(function() {
    if (!$('#map').length) {
      return;
    }
    return ymaps.ready(function() {
      var clusterer, filter, find_goods, search_timeout;
      (function() {
        var map_resize;
        map_resize = function() {
          var w;
          w = $(window).width();
          return $('#map').css({
            width: w,
            marginLeft: 500 - w / 2
          });
        };
        map_resize();
        return $(window).resize(map_resize);
      })();
      window.map = new ymaps.Map('map', {
        center: [50.4505, 30.523],
        zoom: 13,
        controls: ['geolocationControl', 'fullscreenControl', 'typeSelector', 'zoomControl']
      });
      map.behaviors.disable('scrollZoom');
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          return map.panTo([position.coords.latitude, position.coords.longitude]);
        }, function() {}, {
          enableHighAccuracy: true,
          timeout: 30 * 60 * 1000
        });
      }
      map.icons_shape = new ymaps.shape.Polygon(new ymaps.geometry.pixel.Polygon([[[17 - 24, 0 - 58], [30 - 24, 0 - 58], [41 - 24, 7 - 58], [47 - 24, 18 - 58], [47 - 24, 29 - 58], [42 - 24, 38 - 58], [24 - 24, 57 - 58], [8 - 24, 42 - 58], [0 - 24, 30 - 58], [0 - 24, 18 - 58], [6 - 24, 7 - 58], [17 - 24, 0 - 58]]]));
      map.geoObjects.add(new ymaps.Placemark([50.487124, 30.596273], {
        hintContent: 'Благодійний фонд Карітас-Київ'
      }, {
        iconLayout: 'default#image',
        iconImageHref: '/components/modules/Home/includes/img/destination.png',
        iconImageSize: [60, 58],
        iconImageOffset: [-24, -58],
        iconImageShape: map.icons_shape,
        balloonLayout: ymaps.templateLayoutFactory.createClass("<section class=\"home-page-map-balloon-container centers\">\n	<header><h1>Благодійний фонд Карітас-Київ</h1> <a class=\"uk-close\" onclick=\"map.balloon.close()\"></a></header>\n	<article>\n		<address>вулиця Івана Микитенка, 7б</address>\n		<time>Будні: з 9:00 до 18:00<br>Вихідні: з 10:00 до 15:00</time>\n	</article>\n</section>")
      }));
      map.geoObjects.add(new ymaps.Placemark([50.461404, 30.519216], {
        hintContent: 'Книжковий магазин Свічадо'
      }, {
        iconLayout: 'default#image',
        iconImageHref: '/components/modules/Home/includes/img/destination.png',
        iconImageSize: [60, 58],
        iconImageOffset: [-24, -58],
        iconImageShape: map.icons_shape,
        balloonLayout: ymaps.templateLayoutFactory.createClass("<section class=\"home-page-map-balloon-container centers\">\n	<header><h1>Книжковий магазин Свічадо</h1> <a class=\"uk-close\" onclick=\"map.balloon.close()\"></a></header>\n	<article>\n		<address>вулиця Покровська, 6</address>\n		<time>Будні: з 10:00 до 17:00</time>\n	</article>\n</section>")
      }));
      filter = $('.cs-home-page-filter');
      filter.find('input[name=date]').pickmeup({
        format: 'd.m.Y',
        change: function(formated) {
          return filter.find('[name=date]').val(formated).pickmeup('hide').change();
        }
      });
      filter.find('[name=time]').next().find('a').click(function() {
        return filter.find('[name=time]').val($(this).text()).change();
      });
      clusterer = new ymaps.Clusterer();
      clusterer.createCluster = function(center, geoObjects) {
        var cluster;
        cluster = ymaps.Clusterer.prototype.createCluster.call(this, center, geoObjects);
        cluster.options.set({
          icons: [
            {
              href: '/components/modules/Home/includes/img/cluster-46.png',
              size: [46, 46],
              offset: [-23, -23]
            }, {
              href: '/components/modules/Home/includes/img/cluster-58.png',
              size: [58, 58],
              offset: [-27, -27]
            }
          ]
        });
        return cluster;
      };
      map.geoObjects.add(clusterer);
      find_goods = function() {
        var goods;
        goods = $('.cs-home-page-map-goods-switcher.driver .uk-active input').val();
        if (goods === 'my') {
          $('#map, .cs-home-page-filter').hide();
          $('.cs-home-page-my-goods').html('<p class="uk-margin cs-center"><i class="uk-icon-spin uk-icon-spinner"></i></p>').show();
        } else {
          $('#map, .cs-home-page-filter').show();
          $('.cs-home-page-my-goods').hide().html('');
        }
        return $.ajax({
          url: 'api/Home/find_goods',
          data: {
            date: filter.find('input[name=date]').val(),
            time: filter.find('[name=time]').val(),
            goods: goods
          },
          type: 'get',
          success: function(result) {
            var admin, content, good, icon_h_offset, icon_number, icon_v_offset, placemarks, reservation, state, _i, _j, _len, _len1;
            if (result && result.length) {
              if (goods !== 'my') {
                placemarks = [];
                for (_i = 0, _len = result.length; _i < _len; _i++) {
                  good = result[_i];
                  icon_number = Math.round(Math.random() * 11);
                  if (window.driver) {
                    reservation = window.driver === parseInt(good.reserved_driver, 10) && good.reserved > (new Date).getTime() / 1000 ? "<button class=\"reserved uk-button\" data-id=\"" + good.id + "\">Зарезервовано</button>" : "<button class=\"reservation uk-button\" data-id=\"" + good.id + "\">Заберу за 24 години</button>";
                  } else {
                    reservation = '';
                  }
                  admin = window.cs.is_admin ? "<span class=\"uk-icon-trash delete-good\" data-id=\"" + good.id + "\"></span>" : '';
                  placemarks.push(new ymaps.Placemark([good.lat, good.lng], {
                    hintContent: window.driver || good.giver === window.volunteer ? good.username + ' ' + good.phone : void 0
                  }, {
                    iconLayout: 'default#image',
                    iconImageHref: '/components/modules/Home/includes/img/map-icons.png',
                    iconImageSize: [60, 58],
                    iconImageOffset: [-24, -58],
                    iconImageClipRect: [[60 * icon_number, 0], [60 * (icon_number + 1), 58]],
                    iconImageShape: map.icons_shape,
                    balloonLayout: window.driver || good.giver === window.volunteer ? ymaps.templateLayoutFactory.createClass("<section class=\"home-page-map-balloon-container\">\n	<header><h1>" + good.username + " <small>" + good.phone + "</small></h1> " + admin + "<a class=\"uk-close\" onclick=\"map.balloon.close()\"></a></header>\n	<article>\n		<address>" + good.address + "</address>\n		<time>" + good.date + " (" + good.time + ")</time>\n		<p>" + good.comment + "</p>\n	</article>\n	<footer>" + reservation + "</footer>\n</section>") : void 0
                  }));
                }
                clusterer.removeAll();
                clusterer.add(placemarks);
              } else {
                content = '';
                for (_j = 0, _len1 = result.length; _j < _len1; _j++) {
                  good = result[_j];
                  if (good.success === '-1' && good.reserved > (new Date).getTime() / 1000) {
                    state = 'Зарезервовано водієм';
                    icon_h_offset = 97;
                  } else {
                    if (good.success !== '-1') {
                      state = 'Доставлено';
                      icon_h_offset = 2 * 97;
                    } else {
                      state = 'Очікує';
                      icon_h_offset = 0;
                    }
                  }
                  icon_v_offset = Math.round(Math.random() * 6) * 97;
                  content += "<aside>\n	<div class=\"icon\" style=\"background-position: -" + icon_h_offset + "px -" + icon_v_offset + "px\"></div>\n	<h2>" + state + "</h2>\n	<span>" + good.phone + "</span>\n	<address>" + good.address + "</address>\n	<time>" + good.date + " (" + good.time + ")</time>\n	<p>" + good.comment + "</p>\n	<p>\n		<button class=\"cs-home-page-delete-good uk-button\"><i class=\"uk-icon-times\"></i></button>\n		<button class=\"cs-home-page-confirm-good uk-button\"><i class=\"uk-icon-check\"></i> Водій забрав речі</button>\n	</p>\n</aside>";
                }
                $('.cs-home-page-my-goods').html(content + content);
              }
            }
          }
        });
      };
      find_goods();
      filter.on('click', '.reservation', function() {
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
      search_timeout = 0;
      filter.on('keyup change', '[name=date], [name=time], .cs-home-page-map-goods-switcher.driver input', function() {
        clearTimeout(search_timeout);
        return search_timeout = setTimeout(find_goods, 300);
      });
      $('.cs-home-page-map-goods-switcher.driver').on('keyup change', 'input', function() {
        clearTimeout(search_timeout);
        return search_timeout = setTimeout(find_goods, 300);
      });
      return $('#map').on('click', '.delete-good', function() {
        if (!window.cs.is_admin) {
          return;
        }
        if (!confirm('Точно видалити?')) {
          return;
        }
        return $.ajax({
          url: 'api/Home/goods/' + $(this).data('id'),
          type: 'delete',
          success: function() {
            return find_goods();
          }
        });
      });
    });
  });

}).call(this);
