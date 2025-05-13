<?php
ob_start();
session_start();
include "Procedures.php";
$proc = new Procedures();
$conn = $proc->getConnection();

if (!isset($_SESSION['id']) || !isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="favicon.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        .tab-container {
            position: relative;
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            padding: 2px;
            background-color: #dadadb;
            border-radius: 9px;
            margin-bottom: 20px;
        }

        .indicator {
            content: "";
            width: 130px;
            height: 28px;
            background: #ffffff;
            position: absolute;
            top: 2px;
            left: 0;
            z-index: 9;
            border: 0.5px solid rgba(0, 0, 0, 0.04);
            box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.12), 0px 3px 1px rgba(0, 0, 0, 0.04);
            border-radius: 7px;
            transition: left 0.3s ease-out;
        }

        .tab {
            width: 130px;
            height: 28px;
            position: absolute;
            z-index: 99;
            outline: none;
            opacity: 0;
        }

        .tab_label {
            width: 130px;
            height: 28px;
            position: relative;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 0;
            font-size: 0.75rem;
            opacity: 0.6;
            cursor: pointer;
            transition: opacity 0.3s ease-out, color 0.3s ease-out;
        }

        .tab_label:hover {
            opacity: 1;

        }

        .tab:checked+.tab_label {
            opacity: 1;
            color: red;

        }

        .tab--1:checked~.indicator {
            left: 2px;
        }

        .tab--2:checked~.indicator {
            left: calc(130px + 2px);
        }

        .tab--3:checked~.indicator {
            left: calc(130px * 2 + 2px);
        }

        .tab--4:checked~.indicator {
            left: calc(130px * 3 + 2px);
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
            font-weight: 600;
        }

        .dropdown-sub,
        .dropdown-sec {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }

        .btn-custom {
            padding: 8px 20px;
            border-radius: 5px;
            font-weight: 500;
        }

        .error-message {
            color: #dc3545;
            margin-top: 10px;
        }

        select.form-control {
            height: auto;
            padding: 8px 12px;
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div class="home-icon-square">
                <a href="home.php" class="text-decoration-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 16 16" fill="#4A4949">
                        <path
                            d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4H2.5z" />
                    </svg>
                </a>
                <style>
                    .container {
                        padding: 1rem;
                    }

                    .d-flex {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 1rem;
                    }

                    .home-icon-square {
                        position: absolute;
                        top: 1rem;
                        left: 2rem;
                        background-color: rgb(236, 236, 236);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border-radius: 10%;
                        transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
                        box-shadow: 0 2px 2px 2px rgba(0, 0, 0, 0.1);
                        
                    }

                    .home-icon-square:hover {
                        transform: scale(1.1);
                        box-shadow: 0 2px 2px 2px rgba(0, 0, 0, 0.1);
                       
                    }

                    .text-decoration-none {
                        text-decoration: none;
                    }

                    svg {
                        width: 100%;
                        height: auto;
                        fill: #4A4949;
                        transition: fill 0.3s ease;
                        
                    }

                    .home-icon-square:hover svg {
                        fill: rgb(235, 87, 87);
                        
                    }

                    
                    @media (min-width: 576px) {
                        .home-icon-square {
                            width: 2rem;
                            height: 2rem;
                        }
                    }

                    @media (min-width: 768px) {
                        .home-icon-square {
                            width: 3rem;
                            height: 3rem;
                        }
                    }

                    @media (min-width: 992px) {
                        .home-icon-square {
                            width: 2.5rem;
                            height: 2.5rem;
                        }
                    }
                </style>
            </div>
        </div>
</body>