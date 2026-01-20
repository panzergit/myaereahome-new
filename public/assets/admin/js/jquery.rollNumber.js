/*
 * @Author: Patrick-Jun
 * @Date: 2020-08-03 11:21:42
 * @Last Modified by: Patrick-Jun
 * @Last Modified time: 2020-11-03 23:49:34
 * @Git: https://github.com/Patrick-Jun/jQuery.rollNumber.git
 */

(function($) {
  $.fn.rollNumber = function(options) {
    let $self = this;
    if (options.number === undefined) return;
    let number = options.number,
        speed = options.speed || 500,
        interval = options.interval || 100,
        fontStyle = options.fontStyle,
        rooms = options.rooms || String(options.number).split('').length,
        _fillZero = !!options.rooms;
    fontStyle.color = fontStyle.color || '#000'; 
    fontStyle['font-size'] = fontStyle['font-size'] || 14;
    // è®¡ç®—å•ä¸ªæ•°å­—å®½åº¦
    $self.css({
      display: 'flex',
      'justify-content': 'center',
      'align-items': 'center',
      'font-size': fontStyle['font-size'],
      color: 'rgba(0,0,0,0)'
    }).text(number);
    let _height = $self.height();
    let space = options.space || _height/2;
    $self.empty(options);

    // æ·»åŠ æ»šåŠ¨å…ƒç´ 
    let numberHtml = '';
    for (let i = 0; i < 10; i++) numberHtml += `<span style="display: block; width: ${ space }px; height: ${ _height }px; line-height: ${ _height }px; text-align: center; ${ Object.keys(fontStyle).join(': inherit; ') + ': inherit;' }">${ i }</span>`;
    numberHtml = `<div class="_number" style="width: ${ space }px; height: ${ _height }px; line-height: ${ _height }px; display: flex; justify-content: center; align-items: center;"><div style="position: relative; width: ${ space }px; height: ${ _height }px; overflow: hidden;"><div style="position: absolute; width: 100%;">${ numberHtml }</div></div></div>`
    
    // å¤„ç†æ•°å­—
    let numArr = String(number).split('');
    if (_fillZero) { // å‰ç½®è¡¥0
      // å½“å«æœ‰å°æ•°æ—¶ï¼Œè¡¥0ä½æ•°åº”è¯¥+1
      if (String(number).indexOf('.') !== -1) rooms++;
      for (let i = numArr.length; i < rooms; i++) {
        numArr.unshift(0);
      }
      number = numArr.join('');
    }
    if (!!options.symbol) { // å«åƒåˆ†ä½
      let appendHtml = [];
      let symbolHtml = `<span style="display: block; width: ${ space }px; height: ${ _height }px; line-height: ${ _height }px; text-align: center; ${ Object.keys(fontStyle).join(': inherit; ') + ': inherit;' }">${ options.symbol }</span>`;
      let dotHtml = `<span style="display: block; width: ${ space }px; height: ${ _height }px; line-height: ${ _height }px; text-align: center; ${ Object.keys(fontStyle).join(': inherit; ') + ': inherit;' }">.</span>`;
      symbolHtml = `<div class="_number" style="width: ${ space }px; height: ${ _height }px; line-height: ${ _height }px; display: flex; justify-content: center; align-items: center;"><div style="position: relative; width: ${ space }px; height: ${ _height }px; overflow: hidden;"><div style="position: absolute; width: 100%;">${ symbolHtml }</div></div></div>`;
      dotHtml = `<div class="_number" style="width: ${ space }px; height: ${ _height }px; line-height: ${ _height }px; display: flex; justify-content: center; align-items: center;"><div style="position: relative; width: ${ space }px; height: ${ _height }px; overflow: hidden;"><div style="position: absolute; width: 100%;">${ dotHtml }</div></div></div>`;
      
      let numarr = String(number).split('.');
      const re = /(-?\d+)(\d{3})/;
      while (re.test(numarr[0])) {
        numarr[0] = numarr[0].replace(re, '$1,$2');
      }
      numArr = (numarr.length > 1 ? numarr[0] + '.' + numarr[1] : numarr[0]).split('');
      for (let i = 0; i < numArr.length; i++) {
        if (isNaN(Number(numArr[i]))) { // åˆ¤æ–­æ˜¯å¦æ˜¯ç¬¦å·
          if (numArr[i] === '.') { // åˆ¤æ–­å°æ•°
            appendHtml.push(dotHtml);
          } else {
            appendHtml.push(symbolHtml);
          }
        } else {
          appendHtml.push(numberHtml);
        }
      }
      $self.append(appendHtml.join('')).css(fontStyle);
    }else {
      $self.append(numberHtml.repeat(rooms)).css(fontStyle);
      // å¤„ç†å°æ•°ç¬¦å·
      if (String(number).indexOf('.') !== -1) {
        $($self.find('._number')[String(number).indexOf('.')]).find('span')[0].innerHTML = '.';
      }
    }

    let domArr = $self.find('._number');

    for (let i = 0; i < domArr.length; i++) {
      setTimeout(function(dom, n) {
        $(dom.children[0].children[0]).animate({
          'top': -_height * n + 'px' // åƒåˆ†ä½*number = NaN px
        }, speed);
      }, interval*(domArr.length - i), domArr[i], numArr[i]);
    }
  }
})(jQuery);