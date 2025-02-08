SPINNER = - \\ \| /
define spinner
  i=0; \
  chars="- \\ | /"; \
  while kill -0 $$! 2>/dev/null; do \
    i=$$(( (i+1) % 4 )); \
    printf "\r[%s] Carregando..." $$(echo $$chars | cut -d' ' -f$$((i+1))); \
    sleep 0.1; \
  done; \
  printf "\r\033[K"; \
  wait $$!; \
  printf "\r[✔] Concluído!\n";
endef

PORT := 9501
IMAGE := phpswoole/swoole
CONTAINER := swoolebootstrap
VPATH := $(shell pwd)

all:
	@echo "\e[1;34m Comandos disponíveis: \e[0m"
	@echo "  make portl   	      	      - 🚪 Lista Portas em uso."
	@echo "  make run <p=file>           - 🏃 Executa a Aplicacao \e[1;34mSwoole\e[0m."
	@echo "  make docl           	      - 📦 Lista Containers ativos do \e[1;34mDocker\e[0m."
	@echo "  make server <p=file>        - 📦 Executa o Container \e[1;34mSwoole\e[0m."
	@echo "  make stop 		      - 🛑 Para o Container \e[1;34mSwoole\e[0m."
	@echo "  make rundock                - 📦 Executa Containers (Postgres, PgAdmin)"
	@echo "  make stopdock               - 🛑 Para Containers (Postgres, PgAdmin)"
	@echo "  make pest		      - 🧪 Executa os Testes do \e[1;34mPE\e[35mST\e[0m."
	@echo "  make pestc		      - ✅ Executa Cobertura de Testes do \e[1;34mPE\e[35mST\e[0m."
	@echo "  make pestf <f=file> <d=dir> - 📜 Rel. de Cobertura de Testes do \e[1;34mPE\e[35mST\e[0m."
	@echo "  make phpstan                - 📊 Roda a Analise do \e[1;34mPhpstan\e[0m."
	@echo "  make phpstanrep	      - 📜 Gera Relatorio de Analise do \e[1;34mPhpstan\e[0m."
	@echo "  make cdump	       	      - 🎵 Executa o dump-autoload do \e[1;32mComposer\e[0m."
	@echo "\n"

portl:
	@echo "Listando Portas..."
	@lsof -i -P -n

run:
	@echo "Executando App \e[1;34mSwoole\e[0m."
	@php index.php

docl:
	@echo "Conatiners Ativos..."
	@docker ps

server:
	@echo "Executando Container \e[1;34mSwoole\e[0m."
	@docker run --rm -v $(VPATH):/app -w /app -p $(PORT):$(PORT) --name $(CONTAINER) $(IMAGE) php $(p)

stop:
	@docker stop $(CONTAINER) || true

rundock:
	@echo "Executando Containers(Postgres, PgAdmin)..."
	@docker compose -f ./docker/docker-compose-run.yaml up -d

stopdock:	
	@echo "Parando Containers(Jaeger, Prometheus, Grafana)..."
	@docker compose -f ./docker/docker-compose-run.yaml down

pest:
	@echo "\n\e[1;34mPE\e[35mST\e[0m"
	@./vendor/bin/pest

pestv:
	@echo "\n\e[1;34mPE\e[35mST\e[0m: verbose"
	@./vendor/bin/pest --verbose

pestc:
	@echo "\n\e[1;34mPE\e[35mST\e[0m: coverage"
	@XDEBUG_MODE=coverage \
	./vendor/bin/pest --coverage

pestf:
	@echo "\n\e[1;34mPE\e[35mST\e[0m: coverage report"
	@XDEBUG_MODE=coverage \
	./vendor/bin/pest --coverage-$(f)=$(d)

pstan:
	@echo "\n\e[1;34mPhpstan\e[0m"
	@./vendor/bin/phpstan

pstanrep:
	@echo "\n\e[1;34mPhpstan\e[0m: baseline"
	@./vendor/bin/phpstan analyse -vv --level 7 \
  --configuration phpstan.neon \
  $(p) --generate-baseline & $(call spinner)

cdump:
	@echo "\n\e[1;32mComposer\e[0m: dump"
	@composer dump-autoload -o & $(call spinner)
	