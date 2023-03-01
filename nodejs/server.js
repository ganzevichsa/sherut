var app = require('express')();

const { readFileSync } = require("fs");
const { createServer } = require("https");
const { Server } = require("socket.io");

const httpsServer = createServer({
    key: readFileSync("../../../../etc/letsencrypt/live/api.sherutbekalut.co.il/privkey.pem"),
    cert: readFileSync("../../../../etc/letsencrypt/live/api.sherutbekalut.co.il/fullchain.pem")
  });

var http = require('https').Server(httpsServer, app);
var io = require('socket.io')(http, {
  cors: {
    origins: "*:*",
  },
  handlePreflightRequest: (req, res) => {
    res.writeHead(200, {
      "Access-Control-Allow-Origin": "*",
      "Access-Control-Allow-Methods": "GET,POST",
      "Access-Control-Allow-Headers": "my-custom-header",
      "Access-Control-Allow-Credentials": true,
    });
    res.end();
  },
});
var Redis = require('ioredis');
var redis = new Redis();

http.listen(3000, function(redis){
    console.log('Listening on Port 3000');

});

io.on('connection', function(socket) {
    console.log('New connection');
    io.emit('New connection');

 });

redis.psubscribe('*', function(err, count) {
});
redis.on('pmessage', function(pattern, channel, message) {
    console.log('Message Recieved: ' + channel + message);
    var result = JSON.parse(message);
    io.emit(channel + ':' + result.event, result.data[0]);
    console.log(channel + ':' + result.event, result.data[0]);

});

redis.on('pnotification', function(pattern, channel, message) {
  console.log('Message notification: ' + channel + message);
  var result = JSON.parse(message);
  io.emit(channel + ':' + result.event, result.data[0]);
  console.log(channel + ':' + result.event, result.data[0]);

});