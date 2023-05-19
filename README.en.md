# iroha Chat
iroha Chat is an AI chat management system that allows users to create and use prompt templates to interact efficiently and effectively with ChatGPT.

## Official Website (Japanese)
https://docs.irohagpt.com/

## System Requirements
* PHP : 7.4 or later
* MySQL : 5.6 or later

## Installation
1. Download and extract framework's source filese.
https://github.com/cakephp/cakephp/releases/tag/2.10.24
2. Download and extract iroha Chat source files.
https://github.com/irohasoft/irohagpt/releases
3. Replace the app directory with the iroha Chat's source files.
4. Modify the database configuration file (app/Config/database.php).
Make sure that an empty database is created on the MySQL server.
5. Modify the openai configuration file (app/Config/openai.php).
Make sure that a API key is created on the OpenAI's website(https://platform.openai.com/).
6. Upload all the files to a public directory on the web server.
7. Open http://(your-domain-name)/install in your web browser.

## Features

### For users.
- Creating templates.
- Chating with AI.
- Show chat history.
- Show information from an administrator.

### For administrators.
- Manage users.
- Manage user groups.
- Manage information.
- System setting

## License
GPLv3
