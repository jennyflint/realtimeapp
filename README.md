## Project Setup (Docker / Laravel Sail)

### Prerequisites
- Docker and Docker Compose installed and running on your machine.

### Installation Steps

** 1. Clone the repository**

```bash
git clone <repository-url>
cd <project-directory>
```

## 2. Install Composer Dependencies

If you do not have PHP and Composer installed locally, run this Docker command to install the project dependencies:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php85-composer:latest \
    composer install --ignore-platform-reqs
```

### 3. Set up Environment Variables
```bash
cp .env.example .env
```


### 4. Start Laravel Sail

```bash
./vendor/bin/sail up -d
```
### 5. Generate Application Key

```bash
./vendor/bin/sail artisan key:generate
```
### 6. Run Database Migrations

```bash
./vendor/bin/sail artisan migrate
```
### 7. Compile Frontend Assets

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

Useful Sail Commands
Stop the containers:

```bash
./vendor/bin/sail down
```

Run an Artisan command:

```bash
./vendor/bin/sail artisan <command>
```


### Running Queues and WebSockets (Laravel Reverb)

If your application relies on background jobs or real-time broadcasting, you will need to start their respective processes. For local development, it is recommended to run these commands in separate terminal tabs while Sail is running.

**1. Start the Queue Worker**
To process background jobs (like sending emails or broadcasting events), run:
```bash
./vendor/bin/sail artisan queue:work
```

### 2. Start the Reverb WebSocket Server
To handle real-time WebSocket connections via Laravel Reverb, run:

```bash
./vendor/bin/sail artisan reverb:start
```