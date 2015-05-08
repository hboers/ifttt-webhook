ifttt-webhook
=============

^:^:A webhook middleware for the ifttt.com service forked from <https://github.com/captn3m0/ifttt-webhook>
Please read the documention there to understand how theis is working.

The following information is passed along by the webhook
in the raw body of the post request in json encoded format.

```json
    {
  "user" : "username specified in ifttt",
  "password" : "password specified in ifttt",
  "description" : "description specified in ifttt",
  "title" : "title generated for the recipe in ifttt",
  "categories" : ["array","of","categories","passed"]
  "tags" : ["array","of","tags","passed"]
    }
```

#Licence
Licenced under GPL. Some portions of the code are from wordpress itself. You should probably host this on your own server, instead of using `ifttt.captnemoin`. I recommend using [Heroku](http://heroku.com) for excellent php hosting. ([Heroku does supports PHP](http://stackoverflow.com/questions/13334052/does-heroku-support-php))

#Use
Clone the git repo to some place, and use that as the 
wordpress installation location in ifttt.com channel settings.

