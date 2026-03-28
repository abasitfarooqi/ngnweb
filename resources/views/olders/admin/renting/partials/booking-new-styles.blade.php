    <style>
        .kbw-signature {
            display: inline-block;
            border: 1px solid #a0a0a0;
            -ms-touch-action: none;
        }

        .kbw-signature-disabled {
            opacity: 0.35;
        }

        .kbw-signature {
            width: 100%;
            height: 100px;
        }

        .signature {
            distance: 5
        }

        ;

        #sigpad canvas {
            width: 100% !important;
            height: 100px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loader {
            border: 16px solid #f3f3f3;
            /* Light grey */
            border-top: 16px solid #3498db;
            /* Blue */
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
        }

        .regplate {
            background-color: #efd83e !important;
            font-size: 16px;
            color: rgb(0, 0, 0) !important;
            text-shadow: 1px 1px 1px rgb(255, 255, 255);
            max-width: 190px;
            min-width: 180px;
            border-radius: 5px;
            padding: 8px;
        }
    </style>
