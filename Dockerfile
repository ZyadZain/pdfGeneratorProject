FROM python:3.12-slim
RUN apt-get update && apt-get install -y \
    libpango-1.0-0 libpangoft2-1.0-0 \
    && pip install weasyprint \
    && rm -rf /var/lib/apt/lists/*
ENTRYPOINT ["weasyprint"]