import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import Layout from '../../components/Layout/Layout';
import LoadingSpinner from '../../components/Common/LoadingSpinner';
import Alert from '../../components/Common/Alert';
import studentService, { Student, StudentFilters } from '../../services/studentService';

const StudentList: React.FC = () => {
  const navigate = useNavigate();
  const [students, setStudents] = useState<Student[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [filters, setFilters] = useState<StudentFilters>({
    page: 1,
    per_page: 10,
  });
  const [totalPages, setTotalPages] = useState(1);
  const [searchTerm, setSearchTerm] = useState('');

  useEffect(() => {
    fetchStudents();
  }, [filters]);

  const fetchStudents = async () => {
    try {
      setLoading(true);
      const response = await studentService.getStudents(filters);
      setStudents(response.data);
      setTotalPages(Math.ceil(response.total / (filters.per_page || 10)));
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to fetch students');
    } finally {
      setLoading(false);
    }
  };

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    setFilters({ ...filters, search: searchTerm, page: 1 });
  };

  const handleFilterChange = (key: keyof StudentFilters, value: any) => {
    setFilters({ ...filters, [key]: value, page: 1 });
  };

  const handlePageChange = (page: number) => {
    setFilters({ ...filters, page });
  };

  const handleViewStudent = (studentId: number) => {
    navigate(`/students/${studentId}`);
  };

  const handleEditStudent = (studentId: number) => {
    navigate(`/students/${studentId}/edit`);
  };

  return (
    <Layout title="Student Management">
      <div className="container">
        {error && <Alert type="error" message={error} onClose={() => setError('')} />}
        
        <div className="page-header">
          <div className="page-title">
            <h2>Students</h2>
            <p>Manage student records and information</p>
          </div>
          <div className="page-actions">
            <button 
              className="btn btn-primary"
              onClick={() => navigate('/students/register')}
            >
              + Add New Student
            </button>
          </div>
        </div>

        <div className="card">
          <div className="card-header">
            <h3>Search & Filter</h3>
          </div>
          <div className="card-body">
            <form onSubmit={handleSearch} className="search-form">
              <div className="form-row">
                <div className="form-group">
                  <input
                    type="text"
                    placeholder="Search by name, admission number, or mobile..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="form-control"
                  />
                </div>
                <div className="form-group">
                  <select
                    value={filters.status || ''}
                    onChange={(e) => handleFilterChange('status', e.target.value)}
                    className="form-control"
                  >
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                  </select>
                </div>
                <div className="form-group">
                  <select
                    value={filters.academic_year || ''}
                    onChange={(e) => handleFilterChange('academic_year', e.target.value)}
                    className="form-control"
                  >
                    <option value="">All Years</option>
                    <option value="2024-25">2024-25</option>
                    <option value="2023-24">2023-24</option>
                  </select>
                </div>
                <button type="submit" className="btn btn-primary">Search</button>
                <button 
                  type="button" 
                  className="btn btn-secondary"
                  onClick={() => {
                    setSearchTerm('');
                    setFilters({ page: 1, per_page: 10 });
                  }}
                >
                  Clear
                </button>
              </div>
            </form>
          </div>
        </div>

        <div className="card">
          <div className="card-header">
            <h3>Student Records ({students.length})</h3>
          </div>
          <div className="card-body">
            {loading ? (
              <LoadingSpinner message="Loading students..." />
            ) : students.length === 0 ? (
              <div className="no-data">
                <p>No students found</p>
              </div>
            ) : (
              <>
                <div className="table-responsive">
                  <table className="table">
                    <thead>
                      <tr>
                        <th>Admission No.</th>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Mobile</th>
                        <th>Date of Birth</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      {students.map((student) => (
                        <tr key={student.id}>
                          <td>{student.admission_number}</td>
                          <td>
                            <div className="student-info">
                              <strong>{student.user.first_name} {student.user.last_name}</strong>
                              <br />
                              <small>{student.user.email}</small>
                            </div>
                          </td>
                          <td>
                            {student.class?.name} {student.class?.section}
                          </td>
                          <td>{student.user.mobile}</td>
                          <td>{new Date(student.user.date_of_birth).toLocaleDateString()}</td>
                          <td>
                            <span className={`status ${student.status}`}>
                              {student.status}
                            </span>
                          </td>
                          <td>
                            <div className="action-buttons">
                              <button
                                className="btn btn-sm btn-primary"
                                onClick={() => handleViewStudent(student.id)}
                              >
                                View
                              </button>
                              <button
                                className="btn btn-sm btn-secondary"
                                onClick={() => handleEditStudent(student.id)}
                              >
                                Edit
                              </button>
                            </div>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>

                {/* Pagination */}
                {totalPages > 1 && (
                  <div className="pagination">
                    <button
                      className="btn btn-sm"
                      disabled={filters.page === 1}
                      onClick={() => handlePageChange((filters.page || 1) - 1)}
                    >
                      Previous
                    </button>
                    
                    <span className="pagination-info">
                      Page {filters.page} of {totalPages}
                    </span>
                    
                    <button
                      className="btn btn-sm"
                      disabled={filters.page === totalPages}
                      onClick={() => handlePageChange((filters.page || 1) + 1)}
                    >
                      Next
                    </button>
                  </div>
                )}
              </>
            )}
          </div>
        </div>
      </div>
    </Layout>
  );
};

export default StudentList;