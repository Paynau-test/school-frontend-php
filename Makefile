# ============================================
# school-frontend-php · Makefile
# ============================================

GH_ORG := Paynau-test

.PHONY: dev stop logs push help

# ── Local Development ───────────────────────

dev:
	@docker compose down 2>/dev/null || true
	@docker compose up -d --build web
	@echo ""
	@echo "Frontend running at http://localhost:8081"
	@echo "  make logs → ver logs"
	@echo "  make stop → detener"

stop:
	@docker compose down
	@echo "Stopped."

logs:
	@docker compose logs -f web

# ── GitHub ──────────────────────────────────

push:
	@if ! gh repo view $(GH_ORG)/school-frontend-php > /dev/null 2>&1; then \
		gh repo create $(GH_ORG)/school-frontend-php --public --source=. --push; \
	else \
		git push origin main; \
	fi

# ── Help ────────────────────────────────────

help:
	@echo ""
	@echo "school-frontend-php commands:"
	@echo ""
	@echo "  make dev    Run in Docker (port 8081)"
	@echo "  make stop   Stop container"
	@echo "  make logs   Tail container logs"
	@echo "  make push   Push to GitHub"
	@echo ""
