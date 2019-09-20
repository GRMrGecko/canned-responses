# Canned Responses
This is a Rocket.Chat App which is designed to add Canned Responses via the `/can` slash command. The app requires a helper which can be installed on a standard LAMP or LEMP stack with SQLite3 or MySQL/MariaDB support.

# Installation
First, install the helper under the `web-api` folder on your server.

1. Configure the options at the top of the `index.php` to fit your needs.
2. Upload to your LAMP/LEMP service provider, it can be under a sub directory if needed or a sub domain/primary domain.
3. Visit the URL under which the helper app resides.
4. Create your first user account which will become your administrator account.
5. Start adding canned responses.

Next, you will need to add the Canned Responses app to your Rocket.Chat instance.

1. Under Rocket.Chat administration, go to General -> Apps and enable development mode to allow apps to be installed which are not in the market place.
2. Follow instructions at https://rocket.chat/docs/developer-guides/developing-apps/getting-started/ to install the apps-cli.
3. Change directory to the Canned Responses directory and run the following.

```bash
rc-apps deploy --url=https://rocket.domain.com -u USER_ACCOUNT -p "PASSWORD"
```

If you are modifying the code, you can run the following to update the App after you have installed it.

```bash
rc-apps deploy --url=https://rocket.domain.com -u USER_ACCOUNT -p "PASSWORD" --update
```

4. In the Rocket.Chat administration, go the Apps -> Canned Responses and enable/set the API URL to your helper's URL with `/api/` added to the end.