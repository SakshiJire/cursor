import React, { useState, useEffect } from 'react';
import styled from 'styled-components';
import { motion } from 'framer-motion';
import { FaTemple, FaMapMarkerAlt, FaCalendarAlt, FaSearch, FaStar } from 'react-icons/fa';
import { GiIndianPalace } from 'react-icons/gi';
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
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
`;

const PageDescription = styled.p`
  font-size: 1.2rem;
  color: #64748b;
  max-width: 700px;
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
    border-color: #7c3aed;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
  }
`;

const SearchIcon = styled(FaSearch)`
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #64748b;
`;

const TemplesGrid = styled.div`
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
`;

const TempleCard = styled(motion.div)`
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(124, 58, 237, 0.1);
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
    background: linear-gradient(135deg, #7c3aed, #a855f7);
  }
`;

const TempleHeader = styled.div`
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 1.5rem;
`;

const TempleName = styled.h3`
  font-size: 1.4rem;
  color: #7c3aed;
  margin-bottom: 0.5rem;
  line-height: 1.3;
  flex: 1;
`;

const TempleIcon = styled.div`
  font-size: 2rem;
  color: #a855f7;
  margin-left: 1rem;
`;

const TempleInfo = styled.div`
  display: flex;
  flex-direction: column;
  gap: 1rem;
`;

const InfoRow = styled.div`
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem;
  background: rgba(124, 58, 237, 0.05);
  border-radius: 10px;
  transition: all 0.3s ease;

  &:hover {
    background: rgba(124, 58, 237, 0.1);
  }
`;

const InfoIcon = styled.div`
  color: #7c3aed;
  font-size: 1.1rem;
  width: 20px;
  display: flex;
  justify-content: center;
`;

const InfoContent = styled.div`
  flex: 1;
`;

const InfoLabel = styled.span`
  font-weight: 600;
  color: #374151;
  font-size: 0.9rem;
`;

const InfoValue = styled.div`
  color: #7c3aed;
  font-weight: 500;
  margin-top: 0.25rem;
`;

const DeityBadge = styled.span`
  background: linear-gradient(135deg, #fef3c7, #fbbf24);
  color: #92400e;
  padding: 0.25rem 0.75rem;
  border-radius: 15px;
  font-size: 0.8rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
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

const StatsBanner = styled.div`
  background: linear-gradient(135deg, rgba(124, 58, 237, 0.1), rgba(168, 85, 247, 0.1));
  border-radius: 20px;
  padding: 2rem;
  margin-bottom: 3rem;
  text-align: center;
`;

const StatsText = styled.h3`
  color: #7c3aed;
  margin-bottom: 0.5rem;
`;

const StatsSubtext = styled.p`
  color: #64748b;
  font-size: 1rem;
`;

const Temples = () => {
  const [temples, setTemples] = useState([]);
  const [filteredTemples, setFilteredTemples] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');

  useEffect(() => {
    const fetchTemples = async () => {
      try {
        const response = await axios.get('/api/temples');
        setTemples(response.data.data);
        setFilteredTemples(response.data.data);
        setLoading(false);
      } catch (error) {
        console.error('Error fetching temples:', error);
        setLoading(false);
      }
    };

    fetchTemples();
  }, []);

  useEffect(() => {
    const filtered = temples.filter(temple =>
      temple.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      temple.location.toLowerCase().includes(searchTerm.toLowerCase()) ||
      temple.deity.toLowerCase().includes(searchTerm.toLowerCase())
    );
    setFilteredTemples(filtered);
  }, [searchTerm, temples]);

  if (loading) {
    return (
      <PageContainer>
        <LoadingContainer>
          <motion.div
            animate={{ rotate: 360 }}
            transition={{ duration: 2, repeat: Infinity, ease: "linear" }}
          >
            <FaTemple style={{ fontSize: '2rem', color: '#7c3aed' }} />
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
          <PageTitle>
            <GiIndianPalace />
            Sacred Temples of Tamil Nadu
          </PageTitle>
          <PageDescription>
            Journey through the divine architecture and spiritual heritage of Tamil Nadu's 
            magnificent temples, each a testament to centuries of devotion and artistic excellence.
          </PageDescription>
        </motion.div>
      </PageHeader>

      <StatsBanner>
        <StatsText>🕉️ Spiritual Heritage Preserved for Millennia</StatsText>
        <StatsSubtext>
          These temples represent over 2000 years of continuous worship, 
          architectural innovation, and cultural preservation.
        </StatsSubtext>
      </StatsBanner>

      <SearchContainer>
        <SearchBox>
          <SearchIcon />
          <SearchInput
            type="text"
            placeholder="Search temples, locations, or deities..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </SearchBox>
      </SearchContainer>

      {filteredTemples.length === 0 ? (
        <NoResults>
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ duration: 0.5 }}
          >
            No temples found matching your search.
          </motion.div>
        </NoResults>
      ) : (
        <TemplesGrid>
          {filteredTemples.map((temple, index) => (
            <TempleCard
              key={temple.id}
              initial={{ opacity: 0, y: 50 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: index * 0.15, duration: 0.6 }}
              whileHover={{ scale: 1.02 }}
            >
              <TempleHeader>
                <div>
                  <TempleName>{temple.name}</TempleName>
                  <DeityBadge>
                    <FaStar size={10} />
                    {temple.deity}
                  </DeityBadge>
                </div>
                <TempleIcon>
                  <FaTemple />
                </TempleIcon>
              </TempleHeader>
              
              <TempleInfo>
                <InfoRow>
                  <InfoIcon>
                    <FaMapMarkerAlt />
                  </InfoIcon>
                  <InfoContent>
                    <InfoLabel>Location</InfoLabel>
                    <InfoValue>{temple.location}</InfoValue>
                  </InfoContent>
                </InfoRow>
                
                <InfoRow>
                  <InfoIcon>
                    <FaCalendarAlt />
                  </InfoIcon>
                  <InfoContent>
                    <InfoLabel>Built In</InfoLabel>
                    <InfoValue>{temple.built}</InfoValue>
                  </InfoContent>
                </InfoRow>
                
                <InfoRow>
                  <InfoIcon>
                    <GiIndianPalace />
                  </InfoIcon>
                  <InfoContent>
                    <InfoLabel>Architectural Style</InfoLabel>
                    <InfoValue>
                      {temple.built.includes('century') ? 'Dravidian Architecture' : 'Ancient Dravidian'}
                    </InfoValue>
                  </InfoContent>
                </InfoRow>
              </TempleInfo>
            </TempleCard>
          ))}
        </TemplesGrid>
      )}
    </PageContainer>
  );
};

export default Temples;