#!/bin/bash

echo "🎯 Tamil Nadu Heritage Portal Demo"
echo "================================="
echo ""
echo "This will start both backend and frontend servers."
echo "Make sure you have Python 3.7+ and Node.js 14+ installed."
echo ""

# Check if Python is available
if ! command -v python3 &> /dev/null; then
    echo "❌ Python3 is not installed. Please install Python 3.7+ first."
    exit 1
fi

# Check if Node.js is available
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js 14+ first."
    exit 1
fi

echo "✅ Python3 found: $(python3 --version)"
echo "✅ Node.js found: $(node --version)"
echo ""

echo "🚀 Starting Tamil Nadu Heritage Portal..."
echo ""
echo "📋 Instructions:"
echo "1. Backend will start on http://localhost:5000"
echo "2. Frontend will start on http://localhost:3000"
echo "3. Open http://localhost:3000 in your browser"
echo "4. Press Ctrl+C in this terminal to stop both servers"
echo ""

# Function to cleanup background processes
cleanup() {
    echo ""
    echo "🛑 Stopping servers..."
    kill $(jobs -p) 2>/dev/null
    exit
}

# Set trap to cleanup on script exit
trap cleanup SIGINT SIGTERM

# Start backend in background
echo "🔧 Starting backend server..."
(cd backend && python3 -m venv venv 2>/dev/null && source venv/bin/activate && pip install -r requirements.txt >/dev/null 2>&1 && python app.py) &
BACKEND_PID=$!

# Wait a bit for backend to start
sleep 3

# Start frontend in background  
echo "🎨 Starting frontend server..."
(cd frontend && npm install >/dev/null 2>&1 && npm start) &
FRONTEND_PID=$!

echo ""
echo "🌟 Servers are starting up..."
echo "📱 Frontend: http://localhost:3000"
echo "🔌 Backend API: http://localhost:5000"
echo ""
echo "Press Ctrl+C to stop all servers"

# Wait for all background jobs
wait