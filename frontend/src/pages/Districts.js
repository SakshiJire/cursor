import React, { useState, useEffect } from 'react';
import styled from 'styled-components';
import { motion } from 'framer-motion';
import { FaMapMarkerAlt, FaUsers, FaRulerCombined, FaSearch } from 'react-icons/fa';
import axios from 'axios';

const PageContainer = styled.div`
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
`;

const PageHeader = styled.div`
  text-align: center;
  margin-bottom: 3rem;
`;

const PageTitle = styled.h1`
  font-size: 3rem;
  color: #1e3a8a;
  margin-bottom: 1rem;
`;

const PageDescription = styled.p`
  font-size: 1.2rem;
  color: #64748b;
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.6;
`;

const SearchContainer = styled.div`
  display: flex;
  justify-content: center;
  margin-bottom: 3rem;
`;

const SearchBox = styled.div`
  position: relative;
  max-width: 400px;
  width: 100%;
`;

const SearchInput = styled.input`
  width: 100%;
  padding: 1rem 1rem 1rem 3rem;
  border: 2px solid #e2e8f0;
  border-radius: 25px;
  font-size: 1rem;
  outline: none;
  transition: all 0.3s ease;

  &:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }
`;

const SearchIcon = styled(FaSearch)`
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #64748b;
`;

const DistrictsGrid = styled.div`
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
`;

const DistrictCard = styled(motion.div)`
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(30, 58, 138, 0.1);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;

  &:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  }

  &:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
  }
`;

const DistrictName = styled.h3`
  font-size: 1.5rem;
  color: #1e3a8a;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
`;

const DistrictInfo = styled.div`
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-top: 1rem;
`;

const InfoItem = styled.div`
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #64748b;
  font-size: 0.9rem;
`;

const InfoIcon = styled.div`
  color: #3b82f6;
  font-size: 1rem;
`;

const InfoLabel = styled.span`
  font-weight: 500;
  color: #374151;
`;

const InfoValue = styled.span`
  color: #1e3a8a;
  font-weight: 600;
`;

const LoadingContainer = styled.div`
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
  font-size: 1.2rem;
  color: #64748b;
`;

const NoResults = styled.div`
  text-align: center;
  padding: 3rem;
  color: #64748b;
  font-size: 1.2rem;
`;

const StatsContainer = styled.div`
  background: linear-gradient(135deg, rgba(30, 58, 138, 0.1), rgba(59, 130, 246, 0.1));
  border-radius: 20px;
  padding: 2rem;
  margin-bottom: 3rem;
  text-align: center;
`;

const StatsGrid = styled.div`
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 2rem;
`;

const StatItem = styled.div`
  padding: 1rem;
`;

const StatNumber = styled.div`
  font-size: 2rem;
  font-weight: bold;
  color: #1e3a8a;
  margin-bottom: 0.5rem;
`;

const StatLabel = styled.div`
  color: #64748b;
  font-weight: 500;
`;

const Districts = () => {
  const [districts, setDistricts] = useState([]);
  const [filteredDistricts, setFilteredDistricts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');

  useEffect(() => {
    const fetchDistricts = async () => {
      try {
        const response = await axios.get('/api/districts');
        setDistricts(response.data.data);
        setFilteredDistricts(response.data.data);
        setLoading(false);
      } catch (error) {
        console.error('Error fetching districts:', error);
        setLoading(false);
      }
    };

    fetchDistricts();
  }, []);

  useEffect(() => {
    const filtered = districts.filter(district =>
      district.name.toLowerCase().includes(searchTerm.toLowerCase())
    );
    setFilteredDistricts(filtered);
  }, [searchTerm, districts]);

  const totalPopulation = districts.reduce((sum, district) => sum + parseInt(district.population), 0);
  const totalArea = districts.reduce((sum, district) => sum + parseFloat(district.area.split(' ')[0]), 0);

  if (loading) {
    return (
      <PageContainer>
        <LoadingContainer>
          <motion.div
            animate={{ rotate: 360 }}
            transition={{ duration: 2, repeat: Infinity, ease: "linear" }}
          >
            Loading districts...
          </motion.div>
        </LoadingContainer>
      </PageContainer>
    );
  }

  return (
    <PageContainer>
      <PageHeader>
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8 }}
        >
          <PageTitle>Districts of Tamil Nadu</PageTitle>
          <PageDescription>
            Explore the diverse districts of Tamil Nadu, each with its unique identity, 
            cultural heritage, and contribution to the state's rich tapestry.
          </PageDescription>
        </motion.div>
      </PageHeader>

      <StatsContainer>
        <h3 style={{ color: '#1e3a8a', marginBottom: '1.5rem' }}>Overview</h3>
        <StatsGrid>
          <StatItem>
            <StatNumber>{districts.length}</StatNumber>
            <StatLabel>Total Districts</StatLabel>
          </StatItem>
          <StatItem>
            <StatNumber>{(totalPopulation / 10000000).toFixed(1)}M</StatNumber>
            <StatLabel>Total Population (Crores)</StatLabel>
          </StatItem>
          <StatItem>
            <StatNumber>{totalArea.toFixed(0)}</StatNumber>
            <StatLabel>Total Area (sq km)</StatLabel>
          </StatItem>
          <StatItem>
            <StatNumber>{(totalPopulation / totalArea).toFixed(0)}</StatNumber>
            <StatLabel>Avg Density (/sq km)</StatLabel>
          </StatItem>
        </StatsGrid>
      </StatsContainer>

      <SearchContainer>
        <SearchBox>
          <SearchIcon />
          <SearchInput
            type="text"
            placeholder="Search districts..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </SearchBox>
      </SearchContainer>

      {filteredDistricts.length === 0 ? (
        <NoResults>
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ duration: 0.5 }}
          >
            No districts found matching your search.
          </motion.div>
        </NoResults>
      ) : (
        <DistrictsGrid>
          {filteredDistricts.map((district, index) => (
            <DistrictCard
              key={district.id}
              initial={{ opacity: 0, y: 50 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: index * 0.1, duration: 0.6 }}
              whileHover={{ scale: 1.02 }}
            >
              <DistrictName>
                <FaMapMarkerAlt />
                {district.name}
              </DistrictName>
              
              <DistrictInfo>
                <InfoItem>
                  <InfoIcon>
                    <FaUsers />
                  </InfoIcon>
                  <div>
                    <InfoLabel>Population:</InfoLabel>
                    <br />
                    <InfoValue>{parseInt(district.population).toLocaleString()}</InfoValue>
                  </div>
                </InfoItem>
                
                <InfoItem>
                  <InfoIcon>
                    <FaRulerCombined />
                  </InfoIcon>
                  <div>
                    <InfoLabel>Area:</InfoLabel>
                    <br />
                    <InfoValue>{district.area}</InfoValue>
                  </div>
                </InfoItem>
              </DistrictInfo>
              
              <InfoItem style={{ marginTop: '1rem', gridColumn: '1 / -1' }}>
                <InfoIcon>
                  <FaMapMarkerAlt />
                </InfoIcon>
                <div>
                  <InfoLabel>Density:</InfoLabel>
                  <InfoValue>
                    {Math.round(parseInt(district.population) / parseFloat(district.area.split(' ')[0]))} people/sq km
                  </InfoValue>
                </div>
              </InfoItem>
            </DistrictCard>
          ))}
        </DistrictsGrid>
      )}
    </PageContainer>
  );
};

export default Districts;