# Executables
DOCKER_COMP = docker compose

# Kubernetes
IMAGE_NAME = app-a-server
IMAGE_TAG  = latest

# Misc
.DEFAULT_GOAL = help
.PHONY: help build up down restart kind-start kind-status k-load k-configmap k-secret k-deploy k-reload k-status k-logs k-logs-db k-forward k-delete k-up k-down

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

k-configmap: ## apply configmap from k8s/configmap.yaml
	@export $$(cat .env | grep -v '^#' | grep -v '^$$' | xargs) && \
	envsubst < k8s/configmap.yaml | kubectl apply -f -

k-secret: ## apply secret from k8s/secret.yaml
	@export $$(cat .env | grep -v '^#' | grep -v '^$$' | xargs) && \
	envsubst < k8s/secret.yaml | kubectl apply -f -

k-deploy: ## Apply all manifests in k8s/ folder (skips configmap + secret, handled by their own targets)
	kubectl apply -f k8s/pod.yaml -f k8s/postgres.yaml

k-reload: build k-load ## Rebuild image, reload into kind and restart pods
	kubectl rollout restart deployment/app-a-deployment
	kubectl rollout status deployment/app-a-deployment

k-refresh: k-configmap k-secret
	kubectl rollout restart deployment/app-a-deployment
	kubectl rollout status deployment/app-a-deployment

k-artisan: ## Run artisan command — usage: make k-artisan CMD="migrate"
	kubectl exec -it $$(kubectl get pod -l app=app-a -o jsonpath='{.items[0].metadata.name}') -- php artisan $(CMD)

k-status: ## Show all pods, deployments and services
	kubectl get pods,deployments,services,pvc

k-logs: ## Tail logs from Laravel pod
	kubectl logs -l app=app-a --tail=50

k-logs-db: ## Tail logs from postgres pod
	kubectl logs -l app=postgressql-db --tail=50

k-forward: ## Forward app to http://localhost:8080
	kubectl port-forward service/app-a-service 8080:7070

k-delete: ## Remove all k8s resources
	kubectl delete -f k8s/pod.yaml -f k8s/postgres.yaml --ignore-not-found
	kubectl delete configmap app-a-config --ignore-not-found
	kubectl delete secret app-a-secret --ignore-not-found

## —— Full k8s workflow ————————————————————————————————————————————————————
k-up: build k-load k-configmap k-secret k-deploy ## Full deploy: build → load → config → secrets → deploy
	@echo "Done — run 'make k-status' to check pods"

k-down: k-delete ## Tear down all k8s resources
	@echo "All resources removed"
