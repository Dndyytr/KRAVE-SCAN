FROM python:3.10-slim

# Set up a new user named "user" with UID 1000
RUN useradd -m -u 1000 user

# Set environment variables for the user and PyTorch cache
ENV HOME=/home/user \
    PATH=/home/user/.local/bin:$PATH \
    TORCH_HOME=/home/user/.cache/torch

WORKDIR /code

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    build-essential \
    && rm -rf /var/lib/apt/lists/*

# Copy requirements and change ownership
COPY --chown=user ./requirements.txt /code/requirements.txt

# Install PyTorch CPU and other requirements
RUN pip install --no-cache-dir --upgrade pip && \
    pip install --no-cache-dir torch torchvision --index-url https://download.pytorch.org/whl/cpu && \
    pip install --no-cache-dir -r /code/requirements.txt

# Copy the rest of the application files and change ownership
COPY --chown=user . /code

# Switch to the non-root user
USER user

# Expose port 7860 (required by Hugging Face Spaces)
EXPOSE 7860

# Run uvicorn on host 0.0.0.0 and port 7860
CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "7860"]
