install:
	bash install.sh
	docker compose up -d --build
	docker compose exec -T web composer install
