#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Stopping PetFriends services...${NC}"

# Stop Laravel queue worker
echo -e "${GREEN}Stopping Laravel queue worker...${NC}"
pkill -f "queue:work"

# Stop Laravel scheduler
echo -e "${GREEN}Stopping Laravel scheduler...${NC}"
pkill -f "schedule:work"

# Stop Laravel websocket server
echo -e "${GREEN}Stopping Laravel websocket server...${NC}"
pkill -f "websockets:serve"

# Stop Laravel development server
echo -e "${GREEN}Stopping Laravel development server...${NC}"
pkill -f "artisan serve"

# Close ports
echo -e "${GREEN}Closing ports...${NC}"

# Close port 8000 (Laravel development server)
lsof -ti:8000 | xargs kill -9 2>/dev/null || true

# Close port 6001 (Laravel websockets)
lsof -ti:6001 | xargs kill -9 2>/dev/null || true

# Close port 3306 (MySQL)
lsof -ti:3306 | xargs kill -9 2>/dev/null || true

# Close port 80 (Apache)
lsof -ti:80 | xargs kill -9 2>/dev/null || true

echo -e "${GREEN}All services and ports stopped!${NC}" 