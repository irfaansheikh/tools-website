const express = require('express');
const bodyParser = require('body-parser');
const fs = require('fs');

const app = express();
app.use(bodyParser.urlencoded({ extended: true }));
app.use(express.static('public')); // Serve static files (e.g., HTML) from 'public' folder

// In-memory storage (replace with a database like MongoDB for production)
let users = [];

app.post('/login', (req, res) => {
  const { email, password } = req.body;
  const user = users.find(u => u.email === email && u.password === password);
  if (user) {
    res.send('<h1>Login Successful!</h1><a href="/logout.html">Logout</a>');
  } else {
    res.status(401).send('Invalid credentials');
  }
});

app.post('/signup', (req, res) => {
  const { username, email, password } = req.body;
  if (users.find(u => u.email === email)) {
    res.status(400).send('Email already registered');
    return;
  }
  users.push({ username, email, password });
  fs.appendFile('users.txt', `${username},${email},${password}\n`, (err) => {
    if (err) console.error(err);
  });
  res.send('<h1>Signup Successful!</h1><a href="/login.html">Login</a>');
});

app.listen(3000, () => {
  console.log('Server running on http://localhost:3000');
});