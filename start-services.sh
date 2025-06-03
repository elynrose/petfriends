#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Starting PetFriends services...${NC}"

# Start Laravel queue worker
echo -e "${GREEN}Starting Laravel queue worker...${NC}"
php artisan queue:work --daemon &

# Start Laravel scheduler
echo -e "${GREEN}Starting Laravel scheduler...${NC}"
php artisan schedule:work &

# Start Laravel websocket server
echo -e "${GREEN}Starting Laravel websocket server...${NC}"
php artisan websockets:serve &

# Start Laravel development server
echo -e "${GREEN}Starting Laravel development server...${NC}"
php artisan serve &

echo -e "${GREEN}All services started!${NC}"
echo -e "${YELLOW}Press Ctrl+C to stop all services${NC}"

# Keep the script running
wait 