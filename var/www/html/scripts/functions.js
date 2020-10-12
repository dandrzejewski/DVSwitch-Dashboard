function toggleField(hideObj,showObj) {
  hideObj.disabled=true;
  hideObj.style.display='none';
  showObj.disabled=false;
  showObj.style.display='inline';
  showObj.focus();
}
function checkPass(){                   //used in confirm matching password entries
  var pass1 = document.getElementById('pass1');
  var pass2 = document.getElementById('pass2');
  //var message = document.getElementById('confirmMessage');
  //var button = document.getElementById('submitpwd');
  var goodColor = "#66cc66";
  var badColor = "#ff6666";
  if((pass1.value != '') && (pass1.value == pass2.value)){
    pass2.style.backgroundColor = goodColor;
    //message.style.color = goodColor;
    //message.innerHTML = "Passwords Match!";
    //button.disabled = false;
    document.getElementById('submitpwd').removeAttribute("disabled");
 }else{
    pass2.style.backgroundColor = badColor;
    //message.style.color = badColor;
    //message.innerHTML = "Passwords Do Not Match!";
    //button.disabled = true;
    document.getElementById('submitpwd').setAttribute("disabled","disabled");
  }
}

function PCMPlayer(t){this.init(t)}PCMPlayer.prototype.init=function(t){this.option=Object.assign({},{encoding:"16bitInt",channels:1,sampleRate:8e3,flushingTime:1e3},t),this.samples=new Float32Array,this.flush=this.flush.bind(this),this.interval=setInterval(this.flush,this.option.flushingTime),this.maxValue=this.getMaxValue(),this.typedArray=this.getTypedArray(),this.createContext()},PCMPlayer.prototype.getMaxValue=function(){var t={"8bitInt":128,"16bitInt":32768,"32bitInt":2147483648,"32bitFloat":1};return t[this.option.encoding]?t[this.option.encoding]:t["16bitInt"]},PCMPlayer.prototype.getTypedArray=function(){var t={"8bitInt":Int8Array,"16bitInt":Int16Array,"32bitInt":Int32Array,"32bitFloat":Float32Array};return t[this.option.encoding]?t[this.option.encoding]:t["16bitInt"]},PCMPlayer.prototype.createContext=function(){this.audioCtx=new(window.AudioContext||window.webkitAudioContext),this.gainNode=this.audioCtx.createGain(),this.gainNode.gain.value=1,this.gainNode.connect(this.audioCtx.destination),this.startTime=this.audioCtx.currentTime},PCMPlayer.prototype.isTypedArray=function(t){return t.byteLength&&t.buffer&&t.buffer.constructor==ArrayBuffer},PCMPlayer.prototype.feed=function(t){if(this.isTypedArray(t)){t=this.getFormatedValue(t);var e=new Float32Array(this.samples.length+t.length);e.set(this.samples,0),e.set(t,this.samples.length),this.samples=e}},PCMPlayer.prototype.getFormatedValue=function(t){t=new this.typedArray(t.buffer);var e,i=new Float32Array(t.length);for(e=0;e<t.length;e++)i[e]=t[e]/this.maxValue;return i},PCMPlayer.prototype.volume=function(t){this.gainNode.gain.value=t},PCMPlayer.prototype.destroy=function(){this.interval&&clearInterval(this.interval),this.samples=null,this.audioCtx.close(),this.audioCtx=null},PCMPlayer.prototype.flush=function(){if(this.samples.length){var t,e,i,n,a,s=this.audioCtx.createBufferSource(),r=this.samples.length/this.option.channels,o=this.audioCtx.createBuffer(this.option.channels,r,this.option.sampleRate);for(e=0;e<this.option.channels;e++)for(t=o.getChannelData(e),i=e,a=50,n=0;n<r;n++)t[n]=this.samples[i],n<50&&(t[n]=t[n]*n/50),r-51<=n&&(t[n]=t[n]*a--/50),i+=this.option.channels;this.startTime<this.audioCtx.currentTime&&(this.startTime=this.audioCtx.currentTime),console.log("start vs current "+this.startTime+" vs "+this.audioCtx.currentTime+" duration: "+o.duration),s.buffer=o,s.connect(this.gainNode),s.start(this.startTime),this.startTime+=o.duration,this.samples=new Float32Array}};

function playAudio(btn) {
    btn.style.backgroundColor = "green";
   var socketURL = 'ws://' + document.location.hostname + ':8080';
   var player = new PCMPlayer({
        encoding: '16bitInt',
        channels: 1,
        sampleRate: 8000,
        flushingTime: 2000
   });

   var ws = new WebSocket(socketURL);
       ws.binaryType = 'arraybuffer';
       ws.addEventListener('message',function(event) {
            var data = new Uint8Array(event.data);
            player.feed(data);
       });
       
       ws.onclose = function(){
            // connection closed, discard old websocket and create a new one in 5s
            ws = null
            btn.style.backgroundColor = "#b00";
            setTimeout(function() {
                playAudio(btn);
            }, 5000)
        }
 }   
