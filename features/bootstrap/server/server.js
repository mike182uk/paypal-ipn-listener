var express = require('express');
var bodyParser = require('body-parser');

var app = express();

app.use(bodyParser.urlencoded({
  extended: true
}));

app.post('/*', function (req, res) {
  var verified = req.body.__verified;
  var content = '';
  var status = 200;

  if (verified === '1') {
    content = 'VERIFIED';
  } else {
    content = 'INVALID';
  }

  res.status(status).send(content);
});

app.listen(process.env.MOCK_SERVER_PORT);
