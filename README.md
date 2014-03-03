LLOOGG realtime log analyzer web app
===

LLOGG was web service I (Salvatore Sanfilippo) and my co-founder Fabio Pitrola ran for seven years for free. It started as a side project while we were doing different things for our company: we wrote the code in a matter of a few days, and tried to put it online to see the reactions.

The site concept was simple but very addictive: LLOOGG displays the accesses on your web site as they happen, in a pretty raw format. It was one of the first services of this type ever when we created it back in 2007. Later we modified it in order to track Adsense clicks (a feature that was later removed and is not available in this source release). Basically it was very instructive to see what users did in your web site, it makes you able to capture patterns that are hard to capture with aggregated data as processed by Google Analytics.

If you want to see the service running, use demo/demo as username and password at http://demo.lloogg.com. The website does not allow users to register, it is just a copy we ran for a few friends.

Why we closed
---

After the code was put online and the users started to jump and report that they were totally addicted to the service, unfortunately we were already doing other stuff. Fabio was doing new things and moved to Barcelona, while I was writing Redis, exactly because LLOOGG was not scalable using MySQL in our experience, and this stimulated me to think at a different kind of database, that I later applied to LLOOGG with good success.

So long story short, if not for the port from MySQL to Redis and other little things, the service remained running without changes for years.

While LLOOGG is a very lightweight PHP/Redis application, that was able to process 2 billions of pageviews from the connected sites over 5 years since the migration to Redis in a single cheap virtual machine, still the cost of running it, that was about 150 dollars per month, after many years started to annoy me and Fabio, so we discontinued the service.

However there are still users that wanted to use LLOOGG, so even if the **code is completely embarassing** we decided to release it.

But it served as Redis test bed...
---

Yep, in theory at least. The LLOOGG web site was the only one where I was applying Redis directly to have a considerable traffic (350-400 commands per second when it was shut down), so I was running always Redis unstable releases in LLOOGG in order to catch bugs before to put releases in production for other users.

The reality is that not a single Redis bug was found using LLOOGG, it ran always like oil, not a crash, not a latency issue, nothing, so there was no real benefit about this. At this point the majority of issues in Redis are discovered by users using Redis at a much bigger scale.

Why the code is so bad?
---

There are a couple of reasons:

* The code was written in the spare time in a very casual way.
* It started as a fork of another web site developed in back to early 2000 with terrible coding practices.
* It was ported from MySQL to Redis with the logic of "minimal changes".

Part of the lameness of the code is that Redis was very limited when the port was performed, there were no hashes for example. The code uses Redis in a very suboptimal way. The client library was very raw as well, so a connection to Redis is created at every page view.

However there are good thing about this code as well. The internals and the Javascript are pretty ugly but they managed to run for 7 years seeing generations of new browsers and mobile devices without any incompatibility. It is also very simple in the design, so while a mess, we are confident that good programmers will be able to modify it in a matter of minutes.

Before the release I removed tens of useless functions I found inside the code in the hope, at least, to avoid confusing the reader with total garbage.

How to install LLOOGG?
---

LLOOGG is a pure PHP/Redis application without dependencies AFAIK. Any old version of PHP will do, probably even PHP 5.3. To install it:

* Unpack the code into your web root.
* Setup a Redis server.
* Copy `localconfig.php.example` to `localconfig.php`
* Edit `localconfig.php` and put the Redis host and port.
* Edit `config.php` and set your domain name and the username of the admin user.
* Edit `l.js` and go at the end of the file. Where you see `img.src='http://lloogg.com/recv.php'+args;` replace `lloogg.com` with your domain name.

How to use LLOOGG?
---

Just visit the web site root, create an account, get the Javascript tag and put it in your web site. You should see visits in real time appearing in LLOOGG.

The app still distinguish between PRO and normal accounts, since our idea was to adopt a Freemium business model. When you log with the admin account (as setup in `config.php`) you can set PRO accounts.

PRO accounts have a longer persistent history. Also the app has a feature that let you gain read-only access to other accounts. PRO accounts can monitor multiple other websites while free accounts can monitor a single one.

Why GPL v3?
---

I don't like the GPL license normally, but this time we wanted to see changes merged back into the code because of the kind of service LLOOGG is and because of the reasons that ported to its source code open source release.
