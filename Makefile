# COLORS
GREEN		= \033[1;32m
RED 		= \033[1;31m
ORANGE		= \033[1;33m
CYAN		= \033[1;36m
RESET		= \033[0m

# FOLDER
SRCS_DIR	= ./
ENV_DIR		= ${SRCS_DIR}.env
DOCKER_DEV_DIR	= ${SRCS_DIR}docker-compose.dev.yml
DOCKER_PROD_DIR	= ${SRCS_DIR}docker-compose.prod.yml


# COMMANDS
DOCKER_DEV		=  docker compose -f ${DOCKER_DEV_DIR} -p wp-tracking-consent
DOCKER_PROD		=  docker compose -f ${DOCKER_PROD_DIR} -p wp-tracking-consent

%:
	@:

all: up

up:
	@echo "${GREEN}Starting containers...${RESET}"
	@touch ./.dev
	@${DOCKER_DEV} up -d --remove-orphans

down:
	@echo "${RED}Stopping containers...${RESET}"
	@${DOCKER_DEV} down

stop:
	@echo "${RED}Stopping containers...${RESET}"
	@${DOCKER_DEV} stop

rebuild: delete
	@echo "${GREEN}Rebuilding containers...${RESET}"
	@${DOCKER_DEV} up -d --remove-orphans --build

delete:
	@echo "${RED}Deleting containers...${RESET}"
	@${DOCKER_DEV} down -v --remove-orphans

yarn:
	@echo "${GREEN}Running yarn...${RESET}"
	@${DOCKER_DEV} exec node-latest yarn $(filter-out $@,$(MAKECMDGOALS))

install:
	@echo "${GREEN}Installing dependencies...${RESET}"
	@${DOCKER_DEV} exec -it node-latest bash -c "yarn install"

dev: up
	@echo "${GREEN}Compiling Plugin for development...${RESET}"
	@${DOCKER_DEV} exec -it node-latest bash -c "yarn dev"

build:
	@echo "${GREEN}Compiling Plugin for production...${RESET}"
	@rm -f ./.dev
	@${DOCKER_PROD} up --remove-orphans
	@echo "${GREEN}Plugin compiled for production...${RESET}"
