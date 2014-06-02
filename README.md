GitUpdates
================

The server behind the updates in [Glyphish Gallery](https://github.com/glyphish/gallery).

In order to silently update [Glyphish Gallery](https://github.com/glyphish/gallery), we use [Squirrel/Squirrel.Mac](https://github.com/Squirrel/Squirrel.Mac).  In order to provide users with these updates, as we release them live on GitHub, we use **four** things:

1.  Webhooks
2.  Heroku
3.  PostgreSQL
4.  Squirrel

By using this four different tools, the application can receive the latest releases from `master`, or the pre-releases from `development` if they're feeling edgy (support for pre-release builds hasn't been added yet, but just requires a few simple `if statements` in [index.php](index.php)) right from GitHub.  This way, we don't have to deal with hosting a server, linking binaries, uploading binaries.  Any open source OS X release can be managed and installed right from git.

For some explanation of the different technologies used, continue reading, or you can jump to [Using Updates](#using-updates).

###1.  Webhooks
Webhooks are used in order to point the repository to `https://yourapp.herokuapp.com/scripts/webhook.php` (or `https://domain.com/.../scripts/webhook.php`), or rename [webhook.php](scripts/webhook.php), read the JSON payload, and store the release.

###2.  Heroku
Used to run the PHP, mainly to manage the webhook endpoint, and also to read the Postgres database for updates (to generate required JSON).

###3.  PostgreSQL
Used to store each release, so the user could potentially downgrade, but mainly to be able to present user with optionality of updating to pre-releases or just the latest releases.

###4.  Squirrel
Used to actually update the OS X/Windows application (pick your flavor).  Just point Squirrel to your [index.php](index.php) endpoint and you're good to go.

## GitUpdates vs. Squirrel.Server

GitUpdates is sort of like [Squirrel/Squirrel.Server](https://github.com/Squirrel/Squirrel.Server), except for a few main differences:

- It uses PostgreSQL to store every release of your application. (Instead of a JSON file)
- It's written in PHP. (Instead of Ruby)
- It uses webhooks in order to update your releases instantaneously. (Instead of manually updating)
- If the repository's on GitHub, it'll work. (Instead of uploading and managing via a server)

## Using GitUpdates

### Install Squirrel

Head on over to [Squirrel/Squirrel.Mac](https://github.com/Squirrel/Squirrel.Mac) or [Squirrel/Squirrel.Windows](https://github.com/Squirrel/Squirrel.Windows) (if you're into that sorta thing) and follow the installation instructions in their READMEs.

For Squirrel.Mac, I reccomend using [CocoaPods](http://cocoapods.org) (though not fully tested, based on the dependencies...) in order to install [ReactiveCocoa](http://github.com/ReactiveCocoa/ReactiveCocoa)
and [Mantle](https://github.com/MantleFramework/Mantle), both of which Squirrel depends on.

Add the following to your Podfile:

```ruby
platform :ios, '10.9'

# other pods

pod 'ReactiveCocoa', '~> 2.3'
pod 'Mantle', '~> 1.5'
```

These pods should add the required dependencies for Squirrel.Mac, though as mentioned above, they are not tested.

If the above fails, continue with the standard installation recommended at Squirrel.

### Clone and Upload Scripts

#### Working with Heroku
If you do not already have a Heroku app, you can create one (assuming you have [Heroku Toolbelt](https://toolbelt.heroku.com/) installed), by running `heroku create` in the projects folder.  Or, if you already have a Heroku app, just run `git remote add heroku git@heroku.com:app-name.git`.

In the `.gitignore`, the file that is not included is `secret.php`, named for the contents, which are, well, secret.  The contents of `secret.php` is as follows:

```php
<?php
    $dbCredentials = 'dbname={DATABASE NAME} port=5432 user={USERNAME} password={PASSWORD} sslmode=require';
?>
```

You can generate these details, if using Heroku + Heroku Postgres by typing `heroku pg:credentials {DB NAME}`.  Then, just copy this string into the place of the `$dbCredentials` variable above, and you're all set!  Just remember not to push that file to GitHub if you're using a public repo (you still have to push it to Heroku so you can connect)!


You can then `git push heroku master` to deploy GitUpdates to Heroku, but you're not done yet.

The following table is used in my PostgreSQL table `releases` (I could not for the life of me figure out how to export table schema from Heroku Postgres, so if you know, please share!):

|   Column   |          Type          |
|------------|------------------------|
| version    | character varying(15)  |
| prerelease | boolean                |
| zipball    | text                   |
| homepage   | text                   |
| name       | character varying(255) |
| published  | text                   |

Some of these columns may not be of the right type, but by no means am I a PostgreSQL/MySQL expert, and they still work.

#### Non-Heroku (FTP)
If you don't plan on using Heroku, don't fear, the setup is still easy.  Upload GitUpdate, make sure your server supports PostgreSQL, and then follow the above instructions for Heroku (for the most part).

### Adding your Webhook
To add your webhook, go to your repositories "Settings", and then to "Webhooks & Services".

Add a webhook, set the "Payload URL" to your [webhook.php](scripts/webhook.php) endpoint, the "Content type" to `application/json` ~~and enter a secret (if you'd like)~~ (secrets are not yet supported, though they should be).

When you see

![selection](https://i.imgur.com/jphpdXh.png)

make sure to choose "Let me select individual events." and then **ONLY** select "Release":

![release](https://i.imgur.com/qbIPFc9.png)

After GitHub sends a test ping, which should return a `HTTP 200` response (OK) if you've set up GitUpdates correctly above.  

## Notes
Though GitUpdates is written for Squirrel.Mac or Squirrel.Windows, you may find that you can easily adapt it to work in conjunction with any release managment system you create.

## Support
[Open an issue](https://github.com/glyphish/gitupdates/issues) and we'll try to get back to you within 24 hours.

## Contributors
[Rudd Fawcett](http://ruddfawcett.com). You can find all of his open source projects on [GitHub](https://github.com/ruddfawcett).

## Connect
- Follow [@Glyphish on Twitter](https://twitter.com/glyphish).
- Signup for [Glyphish news](https://confirmsubscription.com/h/r/7C4D8263FEF6DC79).
- Directly [contact Glyphish](https://helloglyphish.wufoo.com/forms/send-a-message-about-glyphish-icons/).
- Browse [available icon sets](http://www.glyphish.com).
