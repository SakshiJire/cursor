import React, { useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import styled from 'styled-components';
import { FaBars, FaTimes, FaSearch } from 'react-icons/fa';
import { motion } from 'framer-motion';

const HeaderContainer = styled.header`
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  background: rgba(30, 58, 138, 0.95);
  backdrop-filter: blur(10px);
  z-index: 1000;
  padding: 0 2rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
`;

const Nav = styled.nav`
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
  height: 80px;
`;

const Logo = styled(Link)`
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 1.5rem;
  font-weight: bold;
  color: white;
  transition: transform 0.3s ease;

  &:hover {
    transform: scale(1.05);
  }
`;

const LogoIcon = styled.div`
  width: 40px;
  height: 40px;
  background: linear-gradient(45deg, #ffffff, #e5e7eb);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  color: #1e3a8a;
  font-size: 1.2rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
`;

const NavLinks = styled.div`
  display: flex;
  gap: 2rem;
  align-items: center;

  @media (max-width: 768px) {
    display: ${props => props.isOpen ? 'flex' : 'none'};
    position: absolute;
    top: 80px;
    left: 0;
    right: 0;
    background: rgba(30, 58, 138, 0.98);
    flex-direction: column;
    padding: 2rem;
    gap: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
  }
`;

const NavLink = styled(Link)`
  color: white;
  font-weight: 500;
  position: relative;
  padding: 0.5rem 1rem;
  border-radius: 25px;
  transition: all 0.3s ease;
  background: ${props => props.isActive ? 'rgba(255, 255, 255, 0.2)' : 'transparent'};

  &:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
  }

  &:after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 50%;
    transform: translateX(-50%);
    width: ${props => props.isActive ? '100%' : '0'};
    height: 2px;
    background: white;
    transition: width 0.3s ease;
  }
`;

const SearchContainer = styled.div`
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 25px;
  padding: 0.5rem 1rem;
  margin-left: 2rem;

  @media (max-width: 768px) {
    margin-left: 0;
    margin-top: 1rem;
  }
`;

const SearchInput = styled.input`
  background: transparent;
  border: none;
  color: white;
  padding: 0.25rem 0.5rem;
  outline: none;
  width: 200px;

  &::placeholder {
    color: rgba(255, 255, 255, 0.7);
  }

  @media (max-width: 768px) {
    width: 150px;
  }
`;

const MenuToggle = styled.button`
  display: none;
  background: transparent;
  color: white;
  font-size: 1.5rem;
  padding: 0.5rem;

  @media (max-width: 768px) {
    display: block;
  }
`;

const Header = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const location = useLocation();

  const handleSearch = (e) => {
    e.preventDefault();
    if (searchQuery.trim()) {
      // Implement search functionality here
      console.log('Searching for:', searchQuery);
    }
  };

  const toggleMenu = () => {
    setIsMenuOpen(!isMenuOpen);
  };

  const closeMenu = () => {
    setIsMenuOpen(false);
  };

  return (
    <HeaderContainer>
      <Nav>
        <Logo to="/" onClick={closeMenu}>
          <LogoIcon>TN</LogoIcon>
          <span>Tamil Nadu</span>
        </Logo>
        
        <NavLinks isOpen={isMenuOpen}>
          <NavLink 
            to="/" 
            isActive={location.pathname === '/'}
            onClick={closeMenu}
          >
            Home
          </NavLink>
          <NavLink 
            to="/districts" 
            isActive={location.pathname === '/districts'}
            onClick={closeMenu}
          >
            Districts
          </NavLink>
          <NavLink 
            to="/temples" 
            isActive={location.pathname === '/temples'}
            onClick={closeMenu}
          >
            Temples
          </NavLink>
          <NavLink 
            to="/festivals" 
            isActive={location.pathname === '/festivals'}
            onClick={closeMenu}
          >
            Festivals
          </NavLink>
          <NavLink 
            to="/about" 
            isActive={location.pathname === '/about'}
            onClick={closeMenu}
          >
            About
          </NavLink>
          
          <SearchContainer>
            <form onSubmit={handleSearch}>
              <SearchInput
                type="text"
                placeholder="Search..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
              />
            </form>
            <FaSearch style={{ marginLeft: '0.5rem', opacity: 0.7 }} />
          </SearchContainer>
        </NavLinks>

        <MenuToggle onClick={toggleMenu}>
          {isMenuOpen ? <FaTimes /> : <FaBars />}
        </MenuToggle>
      </Nav>
    </HeaderContainer>
  );
};

export default Header;