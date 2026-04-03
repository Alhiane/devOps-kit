# Executables
DOCKER_COMP = docker compose

# Kubernetes
IMAGE_NAME = app-a-server
IMAGE_TAG  = latest

# Misc
.DEFAULT_GOAL = help
.PHONY: help build up down restart kind-start kind-status k-load k-configmap k-deploy k-status k-logs k-delete k-up k-down

help: ## Show this help
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————
build: ## Build all Docker images
	@$(DOCKER_COMP) build

up: ## Start all containers
	@$(DOCKER_COMP) up -d --remove-orphans

down: ## Stop containers
	@$(DOCKER_COMP) down

restart: down build up ## Restart containers

bash: ## Open bash shell in server container
	@$(DOCKER_COMP) exec server bash

## —— Kubernetes ☸️ ————————————————————————————————————————————————————————
kind-start: ## Start kind cluster (restarts existing or creates new)
	docker start $$(docker ps -aq --filter "name=kind") || kind create cluster

kind-status: ## Check if kind cluster is reachable
	kubectl cluster-info --context kind-kind

k-load: ## Load Docker image into kind cluster
	kind load docker-image $(IMAGE_NAME):$(IMAGE_TAG)

k-configmap:
	kubectl delete configmap app-a-config --ignore-not-found
	kubectl create configmap app-a-config --from-env-file=.env

k-deploy: ## Apply Deployment + Service to cluster
	kubectl apply -f pod.yaml

k-reload: build k-load ## Rebuild image, reload into kind and restart pods
	kubectl rollout restart deployment/app-a-deployment
	kubectl rollout status deployment/app-a-deployment

k-status: ## Show pods, deployments and services
	kubectl get pods,deployments,services -l app=app-a

k-logs: ## Tail logs from running pod
	kubectl logs -l app=app-a --tail=50

k-delete: ## Remove all k8s resources for this app
	kubectl delete -f pod.yaml
	kubectl delete configmap app-a-config

## —— Full k8s workflow ————————————————————————————————————————————————————
k-up: build k-load k-configmap k-deploy ## Full deploy: build image → load → configmap → deploy
	@echo "Done — run 'make k-status' to check pods"

k-down: k-delete ## Tear down all k8s resources
	@echo "All resources removed"
