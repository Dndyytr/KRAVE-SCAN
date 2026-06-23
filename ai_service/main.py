import uvicorn
from fastapi import FastAPI, File, UploadFile, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from model_handler import predict_menu, model

app = FastAPI(
    title="KraveScan AI Image Recognition Service",
    description="Python FastAPI service using MobileNetV2 to identify food menu items from images.",
    version="1.0.0"
)

# Enable CORS for convenience
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

@app.get("/")
def read_root():
    return {
        "status": "healthy",
        "service": "KraveScan AI Service",
        "engine": "MobileNetV2" if model is not None else "Mock Classifier Fallback"
    }

@app.post("/predict")
async def predict(image: UploadFile = File(...)):
    # Validate content type
    if not image.content_type.startswith("image/"):
        raise HTTPException(status_code=400, detail="Uploaded file is not a valid image.")
        
    try:
        image_bytes = await image.read()
        filename = image.filename
        
        result = predict_menu(image_bytes, filename)
        
        return {
            "success": True,
            "prediction": result["prediction"],
            "confidence": result["confidence"],
            "method": result["method"]
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Prediction failed: {str(e)}")

if __name__ == "__main__":
    uvicorn.run("main:app", host="127.0.0.1", port=8000, reload=True)
