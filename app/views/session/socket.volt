<script src="http://cdn.jsdelivr.net/sockjs/1.0.3/sockjs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/stomp.js/2.3.3/stomp.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

<script>
$( function() {
  if (location.search == '?ws') {
      var ws = new WebSocket('ws://192.168.0.233:15674/stomp/websocket');
      console.log(1);
  } else {
      var ws = new SockJS('http://192.168.0.233:15674/stomp');
      console.log(2);
  }

  var ws = new WebSocket('ws://192.168.0.233:15674/stomp/websocket');
  var client = Stomp.over(ws);
  
  client.heartbeat.outgoing = 0;
  client.heartbeat.incoming = 0;

  var onDebug = function(m) {
    console.log('DEBUG', m);
  };
  
  var onConnect = function() {
    client.subscribe('/exchange/halftime-push-exchange/funkibitto', function(d) {
      //console.log(JSON.parse(d.body));
      //alert(JSON.parse(d.body));
    	alert(d.body);
    });
  };
  

  var onError = function(e) {
    console.log('ERROR', e);
  };

  client.debug = onDebug;
  client.connect('halftime_push_service', '0000', onConnect, onError, 'halftime-push');
});
</script>