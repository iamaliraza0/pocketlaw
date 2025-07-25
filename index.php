<?php
include_once 'header.php';
require_once 'config/database.php';
require_once 'classes/Document.php';

$database = new Database();
$db = $database->getConnection();
$document = new Document($db);

$user_documents = $document->getUserDocuments($_SESSION['user_id']);
$document_count = count($user_documents);
$user_name = $_SESSION['user_name'] ?? 'User';

// Get AI conversations count
$conv_query = "SELECT COUNT(*) as count FROM ai_conversations WHERE user_id = ?";
$conv_stmt = $db->prepare($conv_query);
$conv_stmt->execute([$_SESSION['user_id']]);
$ai_conversations_count = $conv_stmt->fetchColumn();
?>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
                    <p class="text-gray-600 mt-1">Welcome back, <?php echo htmlspecialchars($user_name); ?>!</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="openUploadModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-upload mr-2"></i>Upload Document
                    </button>
                    <button onclick="openAIModal()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-robot mr-2"></i>AI Assistant
                    </button>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="p-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm card-hover transition-all duration-300">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800"><?php echo $document_count; ?></p>
                            <p class="text-gray-600 text-sm">Documents</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm card-hover transition-all duration-300">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-robot text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800"><?php echo $ai_conversations_count; ?></p>
                            <p class="text-gray-600 text-sm">AI Conversations</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm card-hover transition-all duration-300">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tasks text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800">5</p>
                            <p class="text-gray-600 text-sm">Active Tasks</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm card-hover transition-all duration-300">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800">92%</p>
                            <p class="text-gray-600 text-sm">Efficiency</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- AI Contract Review -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 gradient-purple rounded-lg flex items-center justify-center">
                            <i class="fas fa-robot text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-lg font-semibold text-gray-800">AI Contract Review</h2>
                            <p class="text-gray-600 text-sm">Get instant AI-powered contract analysis</p>
                        </div>
                    </div>
                    <button onclick="openAIModal()" class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-upload mr-2"></i>Review Contract with AI
                    </button>
                </div>

                <!-- Recent Documents -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-800">Recent Documents</h2>
                        <a href="documents.php" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All</a>
                    </div>
                    <?php if (empty($user_documents)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-file-alt text-3xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 mb-4">No documents uploaded yet</p>
                            <button onclick="openUploadModal()" class="text-blue-600 hover:text-blue-700 font-medium">
                                <i class="fas fa-plus mr-2"></i>Upload Document
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach (array_slice($user_documents, 0, 3) as $doc): ?>
                                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-file-alt text-blue-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800 text-sm"><?php echo htmlspecialchars($doc['original_name']); ?></p>
                                            <p class="text-gray-500 text-xs"><?php echo date('M j, Y', strtotime($doc['created_at'])); ?></p>
                                        </div>
                                    </div>
                                    <button onclick="viewDocument(<?php echo $doc['id']; ?>)" class="text-blue-600 hover:text-blue-700">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- AI Conversations -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Recent AI Conversations</h2>
                    <button onclick="loadConversations()" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Refresh</button>
                </div>
                <div id="conversationsList">
                    <div class="text-center py-8">
                        <i class="fas fa-robot text-3xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Loading conversations...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl max-w-lg w-full">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">Upload Documents</h2>
                    <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center" id="dropZone">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 mb-4">Drag and drop files here, or click to select</p>
                    <input type="file" id="fileInput" multiple class="hidden" accept=".pdf,.doc,.docx,.txt" onchange="handleFileSelect(event)">
                    <button onclick="document.getElementById('fileInput').click()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Select Files
                    </button>
                </div>
                <div id="fileList" class="mt-4 space-y-2"></div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closeUploadModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                    <button onclick="uploadFiles()" id="uploadBtn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50" disabled>
                        <i class="fas fa-upload mr-2"></i>Upload
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AI Modal -->
<div id="aiModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] flex flex-col">
            <div class="p-6 border-b flex justify-between items-center">
                <h2 class="text-xl font-semibold">AI Contract Review</h2>
                <button onclick="closeAIModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="flex flex-1 overflow-hidden">
                <!-- Conversations Sidebar -->
                <div class="w-1/3 border-r bg-gray-50 flex flex-col">
                    <div class="p-4 border-b">
                        <button onclick="startNewConversation()" class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>New Chat
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto p-4">
                        <div id="conversationsSidebar">
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-comments text-2xl mb-2"></i>
                                <p class="text-sm">No conversations yet</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="flex-1 flex flex-col">
                    <div class="flex-1 overflow-y-auto p-6" id="chatMessages">
                        <div class="text-center py-12 text-gray-500">
                            <i class="fas fa-robot text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium mb-2">AI Contract Review Assistant</h3>
                            <p class="mb-4">Upload a contract and ask me to review it for you</p>
                            <button onclick="startNewConversation()" class="text-purple-600 hover:text-purple-700 font-medium">
                                Start New Conversation
                            </button>
                        </div>
                    </div>
                    
                    <!-- Input Area -->
                    <div class="border-t p-6" id="inputArea" style="display: none;">
                        <form id="aiForm" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Contract Document</label>
                                <input type="file" id="contractFile" accept=".pdf,.doc,.docx,.txt" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                                <textarea id="aiInstructions" rows="3" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="What would you like me to review in this contract?"></textarea>
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeAIModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                                <button type="submit" id="aiSubmitBtn" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                    <i class="fas fa-paper-plane mr-2"></i>Send for Review
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedFiles = [];
    let currentConversationId = null;
    let conversations = [];

    function toggleSubmenu(id) {
        const submenu = document.getElementById(id + '-submenu');
        submenu.classList.toggle('hidden');
    }

    // Upload Modal Functions
    function openUploadModal() {
        document.getElementById('uploadModal').classList.remove('hidden');
        setupDragAndDrop();
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').classList.add('hidden');
        selectedFiles = [];
        document.getElementById('fileList').innerHTML = '';
        document.getElementById('uploadBtn').disabled = true;
    }

    function setupDragAndDrop() {
        const dropZone = document.getElementById('dropZone');
        
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropZone.classList.add('border-blue-500', 'bg-blue-50');
        });
        
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            dropZone.classList.remove('border-blue-500', 'bg-blue-50');
        });
        
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropZone.classList.remove('border-blue-500', 'bg-blue-50');
            const files = Array.from(e.dataTransfer.files);
            handleFiles(files);
        });
    }

    function handleFileSelect(event) {
        const files = Array.from(event.target.files);
        handleFiles(files);
    }

    function handleFiles(files) {
        selectedFiles = files;
        displayFileList();
        document.getElementById('uploadBtn').disabled = files.length === 0;
    }

    function displayFileList() {
        const fileList = document.getElementById('fileList');
        fileList.innerHTML = '';
        
        selectedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between p-2 bg-gray-50 rounded';
            fileItem.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-file mr-2 text-gray-500"></i>
                    <span class="text-sm">${file.name}</span>
                    <span class="text-xs text-gray-500 ml-2">(${(file.size / 1024).toFixed(1)} KB)</span>
                </div>
                <button onclick="removeFile(${index})" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            `;
            fileList.appendChild(fileItem);
        });
    }

    function removeFile(index) {
        selectedFiles.splice(index, 1);
        displayFileList();
        document.getElementById('uploadBtn').disabled = selectedFiles.length === 0;
    }

    function uploadFiles() {
        if (selectedFiles.length === 0) return;

        const formData = new FormData();
        selectedFiles.forEach((file, index) => {
            formData.append('documents[]', file);
        });

        const uploadBtn = document.getElementById('uploadBtn');
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
        uploadBtn.disabled = true;

        fetch('api/upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Documents uploaded successfully!');
                closeUploadModal();
                location.reload();
            } else {
                throw new Error(data.error || 'Upload failed');
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            alert('Error uploading documents: ' + error.message);
        })
        .finally(() => {
            uploadBtn.innerHTML = '<i class="fas fa-upload mr-2"></i>Upload';
            uploadBtn.disabled = false;
        });
    }

    // AI Modal Functions
    function openAIModal() {
        document.getElementById('aiModal').classList.remove('hidden');
        loadConversationsSidebar();
    }

    function closeAIModal() {
        document.getElementById('aiModal').classList.add('hidden');
        currentConversationId = null;
    }

    function startNewConversation() {
        currentConversationId = null;
        document.getElementById('chatMessages').innerHTML = `
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-robot text-4xl mb-4"></i>
                <h3 class="text-lg font-medium mb-2">New Conversation</h3>
                <p class="mb-4">Upload a contract document and provide instructions for AI review</p>
            </div>
        `;
        document.getElementById('inputArea').style.display = 'block';
        document.getElementById('aiForm').reset();
    }

    function loadConversationsSidebar() {
        fetch('api/get_conversations.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                conversations = data.conversations;
                displayConversationsSidebar();
            }
        })
        .catch(error => {
            console.error('Error loading conversations:', error);
        });
    }

    function displayConversationsSidebar() {
        const sidebar = document.getElementById('conversationsSidebar');
        
        if (conversations.length === 0) {
            sidebar.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-comments text-2xl mb-2"></i>
                    <p class="text-sm">No conversations yet</p>
                </div>
            `;
            return;
        }

        sidebar.innerHTML = conversations.map(conv => `
            <div class="conversation-item p-3 rounded-lg cursor-pointer hover:bg-white mb-2 ${currentConversationId == conv.id ? 'bg-white border-l-4 border-purple-500' : ''}" 
                 onclick="loadConversation(${conv.id})">
                <h4 class="font-medium text-gray-800 text-sm truncate">${conv.title}</h4>
                <p class="text-xs text-gray-500 mt-1">${new Date(conv.updated_at).toLocaleDateString()}</p>
                <p class="text-xs text-gray-600 mt-1 truncate">${conv.last_message || 'No messages'}</p>
            </div>
        `).join('');
    }

    function loadConversation(conversationId) {
        currentConversationId = conversationId;
        
        fetch(`api/get_conversations.php?conversation_id=${conversationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayMessages(data.messages);
                document.getElementById('inputArea').style.display = 'block';
                displayConversationsSidebar(); // Refresh to show active state
            }
        })
        .catch(error => {
            console.error('Error loading conversation:', error);
        });
    }

    function displayMessages(messages) {
        const chatMessages = document.getElementById('chatMessages');
        
        if (messages.length === 0) {
            chatMessages.innerHTML = `
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-robot text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium mb-2">No messages yet</h3>
                    <p>Start the conversation by uploading a document</p>
                </div>
            `;
            return;
        }

        chatMessages.innerHTML = messages.map(message => {
            const isUser = message.message_type === 'user';
            return `
                <div class="mb-6 ${isUser ? 'text-right' : 'text-left'}">
                    <div class="inline-block max-w-3xl">
                        <div class="flex items-start ${isUser ? 'flex-row-reverse' : 'flex-row'} space-x-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center ${isUser ? 'bg-purple-500 text-white ml-3' : 'bg-gray-200 text-gray-600 mr-3'}">
                                <i class="fas ${isUser ? 'fa-user' : 'fa-robot'} text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="bg-${isUser ? 'purple-500 text-white' : 'gray-100 text-gray-800'} rounded-lg p-4">
                                    <div class="prose prose-sm max-w-none ${isUser ? 'text-white' : ''}">
                                        ${message.content.replace(/\n/g, '<br>')}
                                    </div>
                                    ${message.document_name ? `<div class="mt-2 text-xs opacity-75"><i class="fas fa-file mr-1"></i>${message.document_name}</div>` : ''}
                                </div>
                                <div class="text-xs text-gray-500 mt-1 ${isUser ? 'text-right' : 'text-left'}">
                                    ${new Date(message.created_at).toLocaleString()}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
        
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // AI Form Submission
    document.getElementById('aiForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const contractFile = document.getElementById('contractFile').files[0];
        const instructions = document.getElementById('aiInstructions').value.trim();
        
        if (!contractFile) {
            alert('Please select a contract document');
            return;
        }
        
        if (!instructions) {
            alert('Please provide instructions for the AI review');
            return;
        }
        
        const formData = new FormData();
        formData.append('document', contractFile);
        formData.append('instructions', instructions);
        if (currentConversationId) {
            formData.append('conversation_id', currentConversationId);
        }
        
        const submitBtn = document.getElementById('aiSubmitBtn');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
        submitBtn.disabled = true;
        
        fetch('api/ai_contract_review.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentConversationId = data.conversation_id;
                loadConversation(currentConversationId);
                loadConversationsSidebar();
                document.getElementById('aiForm').reset();
            } else {
                throw new Error(data.error || 'AI review failed');
            }
        })
        .catch(error => {
            console.error('AI review error:', error);
            alert('Error processing AI review: ' + error.message);
        })
        .finally(() => {
            submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Send for Review';
            submitBtn.disabled = false;
        });
    });

    // Load conversations on page load
    function loadConversations() {
        fetch('api/get_conversations.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayRecentConversations(data.conversations);
            }
        })
        .catch(error => {
            console.error('Error loading conversations:', error);
            document.getElementById('conversationsList').innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                    <p>Error loading conversations</p>
                </div>
            `;
        });
    }

    function displayRecentConversations(conversations) {
        const container = document.getElementById('conversationsList');
        
        if (conversations.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-robot text-3xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500 mb-4">No AI conversations yet</p>
                    <button onclick="openAIModal()" class="text-purple-600 hover:text-purple-700 font-medium">
                        <i class="fas fa-plus mr-2"></i>Start AI Review
                    </button>
                </div>
            `;
            return;
        }

        container.innerHTML = conversations.slice(0, 5).map(conv => `
            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg cursor-pointer" onclick="openConversationInModal(${conv.id})">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-robot text-purple-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 text-sm">${conv.title}</p>
                        <p class="text-gray-500 text-xs">${new Date(conv.updated_at).toLocaleDateString()}</p>
                    </div>
                </div>
                <span class="text-xs text-gray-400">${conv.message_count} messages</span>
            </div>
        `).join('');
    }

    function openConversationInModal(conversationId) {
        openAIModal();
        setTimeout(() => {
            loadConversation(conversationId);
        }, 100);
    }

    function viewDocument(id) {
        window.open(`api/view.php?id=${id}`, '_blank');
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        loadConversations();
    });
</script>