import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import styled, { createGlobalStyle } from 'styled-components';
import Header from './components/Header';
import Home from './pages/Home';
import Districts from './pages/Districts';
import Temples from './pages/Temples';
import Festivals from './pages/Festivals';
import About from './pages/About';

const GlobalStyle = createGlobalStyle`
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    color: #2c3e50;
  }

  h1, h2, h3, h4, h5, h6 {
    color: #1e3a8a;
  }

  a {
    text-decoration: none;
    color: inherit;
  }

  button {
    cursor: pointer;
    border: none;
    outline: none;
  }
`;

const AppContainer = styled.div`
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
`;

const MainContent = styled.main`
  padding-top: 80px;
  min-height: calc(100vh - 80px);
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
`;

function App() {
  return (
    <Router>
      <AppContainer>
        <GlobalStyle />
        <Header />
        <MainContent>
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/districts" element={<Districts />} />
            <Route path="/temples" element={<Temples />} />
            <Route path="/festivals" element={<Festivals />} />
            <Route path="/about" element={<About />} />
          </Routes>
        </MainContent>
      </AppContainer>
    </Router>
  );
}

export default App;