from fastapi import FastAPI, UploadFile, Request
from fastapi.responses import FileResponse, HTMLResponse
from fastapi.staticfiles import StaticFiles
import cv2, pytesseract, csv, os
import numpy as np
from datetime import datetime

app = FastAPI()

# Serve static frontend
app.mount("/static", StaticFiles(directory="static"), name="static")

CSV_FILE = "scrabble_games.csv"

def preprocess_and_extract_words(image_path: str):
    img = cv2.imread(image_path)
    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)

    # Improve readability
    blur = cv2.GaussianBlur(gray, (3, 3), 0)
    _, binary = cv2.threshold(blur, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)

    # Invert for OCR
    thresh = 255 - binary

    h, w = thresh.shape
    cell_h, cell_w = h // 15, w // 15

    board = [["" for _ in range(15)] for _ in range(15)]

    for r in range(15):
        for c in range(15):
            cell = thresh[r*cell_h:(r+1)*cell_h, c*cell_w:(c+1)*cell_w]
            config = "--psm 10 -c tessedit_char_whitelist=ABCDEFGHIJKLMNOPQRSTUVWXYZ"
            text = pytesseract.image_to_string(cell, config=config).strip().upper()
            if text and text.isalpha():
                board[r][c] = text

    words = []
    # Horizontal scan
    for row in board:
        seq = "".join(ch if ch else " " for ch in row)
        for part in seq.split():
            if len(part) >= 2:
                words.append(part)

    # Vertical scan
    for c in range(15):
        col = "".join(board[r][c] if board[r][c] else " " for r in range(15))
        for part in col.split():
            if len(part) >= 2:
                words.append(part)

    # Bucket by length
    words_by_length = {}
    for w in words:
        words_by_length.setdefault(len(w), []).append(w)

    return board, words_by_length


@app.get("/", response_class=HTMLResponse)
async def root():
    with open("static/index.html") as f:
        return HTMLResponse(content=f.read())


@app.post("/process/")
async def process_board(file: UploadFile):
    filename = "board.jpg"
    contents = await file.read()
    with open(filename, "wb") as f:
        f.write(contents)

    board, words_by_length = preprocess_and_extract_words(filename)

    # Append results to CSV
    file_exists = os.path.isfile(CSV_FILE)
    with open(CSV_FILE, "a", newline="") as f:
        writer = csv.writer(f)
        if not file_exists:
            headers = ["datetime"] + [f"{n}-letter words" for n in range(2, 16)]
            writer.writerow(headers)
        row = [datetime.now().isoformat()] + [",".join(words_by_length.get(n, [])) for n in range(2, 16)]
        writer.writerow(row)

    return {
        "csv_file": CSV_FILE,
        "board": board,
        "words_by_length": words_by_length
    }


@app.get("/download/")
async def download_csv():
    if not os.path.isfile(CSV_FILE):
        return {"error": "No CSV yet"}
    return FileResponse(CSV_FILE, media_type="text/csv", filename=CSV_FILE)
