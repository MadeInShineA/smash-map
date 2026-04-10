# What's Smash Map ?

Smash Map is an open source website under development, it's goal is to help smash players to find tournaments and other players around the world 

## Smash Map Features

### Current Smash Map features
- **World map** to display events and registered users
- **Event Browsing** 
-  **User's notifications** to let users know when a tournament is happening near them
- **Automatic event creation** The Smash map database will automatically keep track of the next tournaments

### Future features
- **Add HDR** as a supported game
- **In real time notifications** with WebSockets
- **Event Calendar** to get a better overview of what is happening around the world
- **Manual event creation**

### In progress features
- Maintaining the site
- Bug fixes

## Setup

### Prerequisites

Make sure [**PHP**](https://www.php.net/), [**Composer**](https://getcomposer.org/), [**NodeJS**](https://nodejs.org/en) and [**Docker**](https://www.docker.com/) are installed.

> **Note:** Docker Compose is not included in the nix flake and must be installed separately.

Alternatively, use the provided nix flake for PHP/Composer/NodeJS:
```bash
nix develop
```

> **Note:** Docker must still be installed separately as it is not included in the nix flake.

### 1. Install dependencies

```bash
npm install
composer install
```

### 2. Configure environment

```bash
cp .env.example .env
```

Fill in the `.env` file with your database credentials and other required values.

### 3. Generate application key

```bash
php artisan key:generate
```

### 4. Start the database Docker service

```bash
docker compose up -d
```

### 5. Set up database

> **Note:** The first migration is expected to fail. This is because character migrations (Mewtwo/Roy for Project+) depend on game data (Project+) that is only created by the seeder.

```bash
php artisan migrate        # Fails - missing tables from seeder
php artisan db:seed        # Creates required tables
php artisan migrate        # Completes successfully
php artisan storage:link
```

### 6. Populate the database

```bash
php artisan setup
php artisan import-50-events-all-games
```

### 7. Run the development servers

```bash
npm run dev
php artisan serve
```

## Smash map links

[GitHub](https://github.com/MadeInShineA/smash-map)

[Twitter](https://twitter.com/\_SmashMap\_)

[Discord](https://discord.gg/kNZFk2JYGX)

[Website](https://smash-map.com)
