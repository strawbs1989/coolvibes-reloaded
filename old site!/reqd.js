const express = require('express');
const bodyParser = require('body-parser');
const axios = require('axios');

const app = express();
app.use(bodyParser.json());

const GOOGLE_SCRIPT_URL = 'https://script.google.com/macros/s/AKfycbx0TJ7Af8BgOB3Pq4gDLG9zHpcDhfCLvbp5Xm0XB3koMRqGtqn08QfWxrZu5UkgXWU/exec'; // The URL of your Google Apps Script

app.post('/submit-request', async (req, res) => {
  const { name, songTitle, artist, dedication } = req.body;
  
  try {
    const response = await axios.post(GOOGLE_SCRIPT_URL, {
      name,
      songTitle,
      artist,
      dedication,
    });
    
    res.send(response.data);
  } catch (error) {
    console.error(error);
    res.status(500).send('Error submitting request');
  }
});

const port = process.env.PORT || 3000;
app.listen(port, () => {
  console.log(`Server is running on port ${443}`);
});
