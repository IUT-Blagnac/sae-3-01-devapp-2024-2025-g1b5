package sae;

public class Main {
    public static void main(String[] args) {
        String userDirectoryPath = System.getProperty("user.dir");
		System.out.println("Current Directory = \"" + userDirectoryPath + "\"" );
        App.main2(args);
    }
}