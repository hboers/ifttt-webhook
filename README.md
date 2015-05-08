ifttt-webhook
=============

A webhook middleware for the ifttt.com service forked from <https://github.com/captn3m0/ifttt-webhook>
Please read the documention there to understand how this is working.

## Changes in this fork

The following information is passed along by the webhook
in the raw body of the post request in json encoded format.

```json
    {
  "user" : "username specified in ifttt",
  "password" : "password specified in ifttt",
  "data" : "description specified in ifttt",
    }
```

In Categories the target is specified. The target base url is specified in 
the Application configuration. The first given categorie is appended as
target. 

  <base_url>/<categorie>

#Limitations
Only categories matching [A-Z][a-z][0-9]+ are accepted.
Currently only the first categorie is used.

#Licence
Licenced under GPL. Some portions of the code are from wordpress itself. 
You should host this on your own server. 

#Use
Clone the git repo to some place, setup your webserver and use that as the 
wordpress installation location in ifttt.com channel settings.

# Planning

  * Sending more than one webhook request
  * Allow full urls in categories (seems that IFTTT passes them untouched)

