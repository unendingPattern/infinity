
var altchans = [{name: "8ch.pl",     url: "https://8ch.pl/",      boards: "https://8ch.pl/boards.html"},
                {name: "8ch.net",    url: "https://8ch.net/",     boards: "https://8ch.net/boards.html"},
                {name: "endchan.xyz",url: "https://endchan.xyz/", boards: "https://8ch.pl/proxy.php?f=https://endchan.xyz/boards.js"},
               ];

// localStorage.altchans = [
//              {name: "librechan.net",    url: "https://librechan.net/",    boards: "https://8ch.pl/proxy.php?f=https://librechan.net/boards.html"}];
// ];

altchans = altchans.concat(JSON.parse(localStorage.altchans || "[]"));

$(function() {
  if (active_page == 'page' && $('#boardlist').length) {
    var ib_list = $('<div id="ib-list" class="description box col col-12">');

    var ib_list_title = $('<strong>Alternative boards:</strong>').appendTo(ib_list);

    ib_list.insertBefore('div.board-list');

    altchans.forEach(function(chan) {
      var link = $('<a>').text(chan.name).attr('href', chan.url).appendTo(ib_list);
      link.css('margin-left', 10);

      link.click(function(e) {
        if (e.which != 1) return true;

        var selector = 'div.board-list, div#divBoards';

        $.get(chan.boards, function(data) {
          var bl = $(data).find(selector);
          var oldbl = $(selector).first();
          $(oldbl).before(bl);
          $(oldbl).remove();

          $('base').remove();
          $('<base href="'+chan.url+'">').appendTo('head');

          $('div#divBoards').css('display', 'table').addClass('board-list-table');
          $('div.boardsCell').css('display', 'table-row');
          $('div.boardsCell span').css('display', 'table-cell').css('margin', 2).css('padding', 2);
          $('div#boardsCellHeader').addClass('board-list-head').css('border', 1);
        });
        return false;
      });

    });
  }
});


